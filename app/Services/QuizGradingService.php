<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\QuizQuestion;
use Illuminate\Support\Collection;

/**
 * Service untuk handle auto-grading quiz
 * Mendukung: multiple choice, true/false, essay (manual)
 */
class QuizGradingService
{
    /**
     * Grade a quiz submission
     * @param QuizSubmission $submission
     * @param array $answers Format: ['question_id' => 'answer']
     * @return array ['score' => float, 'total_points' => float, 'details' => Collection]
     */
    public function gradeSubmission(QuizSubmission $submission, array $answers = []): array
    {
        $quiz = $submission->quiz;
        $questions = $quiz->questions;
        
        $totalPoints = 0;
        $earnedPoints = 0;
        $details = [];

        foreach ($questions as $question) {
            $questionPoints = $question->points ?? 1;
            $totalPoints += $questionPoints;

            // Get answer dari submission atau dari parameter
            $userAnswer = $answers[$question->id] ?? $submission->answers[$question->id] ?? null;

            $isCorrect = false;
            $feedback = '';

            // Auto-grade untuk multiple choice dan true/false
            if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                $isCorrect = $this->checkAnswer($question, $userAnswer);
                if ($isCorrect) {
                    $earnedPoints += $questionPoints;
                    $feedback = 'Correct!';
                } else {
                    $feedback = 'Incorrect. Correct answer: ' . $question->correct_answer;
                }
            } 
            // Essay: perlu manual grading
            elseif ($question->question_type === 'essay') {
                $feedback = 'Pending manual grading';
            }

            $details[] = [
                'question_id' => $question->id,
                'question_text' => $question->question,
                'question_type' => $question->question_type,
                'user_answer' => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $questionPoints : 0,
                'points_total' => $questionPoints,
                'feedback' => $feedback
            ];
        }

        $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $isPassed = $percentage >= $quiz->passing_score;

        // Update submission dengan hasil
        $submission->update([
            'score' => $earnedPoints,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'is_passed' => $isPassed,
            'answers' => $answers ?: $submission->answers,
            'graded_at' => now(),
            'graded_by' => 'auto' // untuk multiple choice/true false
        ]);

        return [
            'score' => $earnedPoints,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'is_passed' => $isPassed,
            'details' => collect($details)
        ];
    }

    /**
     * Check if user answer matches correct answer
     * @param QuizQuestion $question
     * @param mixed $userAnswer
     * @return bool
     */
    private function checkAnswer(QuizQuestion $question, $userAnswer): bool
    {
        if (!$userAnswer) {
            return false;
        }

        $correctAnswer = strtolower(trim($question->correct_answer));
        $userAnswerStr = strtolower(trim($userAnswer));

        // For multiple choice (option letter)
        if ($question->question_type === 'multiple_choice') {
            return $userAnswerStr === $correctAnswer;
        }

        // For true/false
        if ($question->question_type === 'true_false') {
            $normalized = ['true' => 'true', 'false' => 'false', 'yes' => 'true', 'no' => 'false'];
            $normalizedUser = $normalized[$userAnswerStr] ?? $userAnswerStr;
            $normalizedCorrect = $normalized[$correctAnswer] ?? $correctAnswer;
            return $normalizedUser === $normalizedCorrect;
        }

        return false;
    }

    /**
     * Get average score for a quiz
     */
    public function getAverageScore(Quiz $quiz): float
    {
        $submissions = $quiz->submissions()->whereNotNull('score')->get();
        
        if ($submissions->isEmpty()) {
            return 0;
        }

        return $submissions->avg('percentage');
    }

    /**
     * Get pass rate for a quiz
     */
    public function getPassRate(Quiz $quiz): float
    {
        $submissions = $quiz->submissions()->whereNotNull('score')->get();
        
        if ($submissions->isEmpty()) {
            return 0;
        }

        $passed = $submissions->where('is_passed', true)->count();
        return ($passed / $submissions->count()) * 100;
    }
}
