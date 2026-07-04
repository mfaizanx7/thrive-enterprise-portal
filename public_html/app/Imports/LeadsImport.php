<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\LeadStage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeadsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    private $pipelineId;
    private $firstStageId;
    private $rowCount = 0;
    private $skippedCount = 0;
    private $createdCount = 0;

    public function __construct()
    {
        $this->pipelineId = 59; // MindStir pipeline ID
        $this->firstStageId = LeadStage::where('pipeline_id', $this->pipelineId)
            ->orderBy('order')
            ->value('id');

        Log::info('LeadsImport initialized. Pipeline ID: ' . $this->pipelineId . ', First Stage ID: ' . ($this->firstStageId ?? 'NULL'));
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->rowCount++;

        // Log the first row's keys to understand the column names
        if ($this->rowCount === 1) {
            Log::info('First row keys: ' . json_encode(array_keys($row)));
            Log::info('First row data: ' . json_encode($row));
        }

        // Handle both numeric array (no header) and associative array (with header)
        // Numeric index mapping based on Excel structure:
        // 0: Sr.No, 1: team_members, 2: Business/Startup, 3: email, 4: full_name, 5: phone, 6: inbox_url, 7: update, 8: follow_up_1, 9: follow_up_2, 10: follow_up_3
        if (array_key_exists(0, $row)) {
            // Numeric array - use index mapping
            $email = $row[3] ?? null;
            $fullName = $row[4] ?? null;
            $srNo = $row[0] ?? null;
            $teamMembers = $row[1] ?? null;
            $subject = $row[2] ?? null;
            $phone = $row[5] ?? null;
            $inboxUrl = $row[6] ?? null;
            $update = $row[7] ?? null;
            $followUp1 = $row[8] ?? null;
            $followUp2 = $row[9] ?? null;
            $followUp3 = $row[10] ?? null;
        } else {
            // Associative array - use column names
            $email = $row['email'] ?? $row['Email'] ?? null;
            $fullName = $row['full_name'] ?? $row['Full Name'] ?? $row['Name'] ?? null;
            $srNo = $row['sr_no'] ?? $row['Sr.No'] ?? null;
            $teamMembers = $row['team_members'] ?? $row['Team Members'] ?? null;
            $subject = $row['business_startup'] ?? $row['Business/Startup'] ?? $row['subject'] ?? null;
            $phone = $row['phone'] ?? $row['Phone'] ?? null;
            $inboxUrl = $row['inbox_url'] ?? $row['Inbox URL'] ?? null;
            $update = $row['update'] ?? $row['Update'] ?? null;
            $followUp1 = $row['follow_up_1'] ?? $row['Follow Up 1'] ?? null;
            $followUp2 = $row['follow_up_2'] ?? $row['Follow Up 2'] ?? null;
            $followUp3 = $row['follow_up_3'] ?? $row['Follow Up 3'] ?? null;
        }

        // Skip empty rows
        if (empty($email) || empty($fullName)) {
            $this->skippedCount++;
            Log::warning('Row ' . $this->rowCount . ' skipped - email or full_name empty. Row: ' . json_encode($row));
            return null;
        }

        $this->createdCount++;
        Log::info('Creating lead from row ' . $this->rowCount . ': ' . $email);

        // Use name as fallback if subject is null
        $finalSubject = $subject ?? $fullName ?? 'No subject';

        return new Lead([
            'name' => $fullName,
            'email' => $email,
            'subject' => $finalSubject,
            'phone' => $phone,
            'inbox_url' => $inboxUrl,
            'team_members' => $teamMembers,
            'sr_no' => $srNo,
            'update_value' => $update,
            'follow_up_1' => $followUp1,
            'update_2_0' => null, // No column for this in current Excel
            'follow_up_2' => $followUp2,
            'follow_up_3' => $followUp3,
            'pipeline_id' => $this->pipelineId,
            'stage_id' => $this->firstStageId,
            'created_by' => Auth::user()->creatorId(),
            'order' => 0,
            'is_active' => 1,
            'date' => now(),
        ]);
    }

    public function chunkSize(): int
    {
        return 100; // Process 100 rows at a time
    }

    public function __destruct()
    {
        Log::info('LeadsImport finished. Total rows: ' . $this->rowCount . ', Created: ' . $this->createdCount . ', Skipped: ' . $this->skippedCount);
    }
}
