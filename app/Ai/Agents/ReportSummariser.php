<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::OpenAI)]
#[Model('gpt-4o-mini')]
class ReportSummariser implements Agent
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
        You are an operations analyst for Constructive Cleaning Company Ltd (CCC).

        Your job is to read job reports and produce concise executive summaries for
        management review.

        Rules:
        - Summarise in 2–4 short paragraphs maximum
        - Cover: what was done, any issues found, and key recommendations
        - Use plain, professional English — avoid jargon
        - Do NOT invent or embellish details not present in the report
        - Output only the summary — no headings, no meta-commentary
        INSTRUCTIONS;
    }
}
