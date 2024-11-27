<?php

namespace App\Http\Middleware;

use Closure;
use App\Entities\CBT\Participant;
use App\Extra\Repositories\TokenRepository;
use Illuminate\Http\Request;

class ParsingSignatureParticipant
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Throwable
     */
    public function handle(Request $request, Closure $next): mixed
    {
        abort_if(! $request->hasHeader('X-Signature-Enroll'), 403, 'Missing header [X-Signature-Enroll]');

        /**
         * @var $user \App\Entities\Account\User
         */
        $user = $request->user();

        /**
         * @var $exam \App\Entities\CBT\Exam
         */
        $exam = $user->exams()
            ->wherePivot('id', $this->getParticipantId($request->header('X-Signature-Enroll')))
            ->first();

        abort_if(! $exam, 500, 'The exam is missing [SYSTEM ERROR]');
        abort_if($exam->detail->status === Participant::STATUS_BANNED, 403,
            'You has been banned from the exam');

        app()->bind(Participant::class, fn () => $exam->detail);

        return $next($request);
    }

    public function getParticipantId($token)
    {
        return TokenRepository::parse($token)->claims()->get('jti');
    }
}
