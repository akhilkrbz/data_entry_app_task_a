<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DataImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $total_rows = 0;
    protected $new_rows = 0;
    protected $updated_rows = 0;

    public function getTotalRows() {
        return $this->total_rows;
    }
    public function getNewRows() {
        return $this->new_rows;
    }
    public function getUpdatedRows() {
        return $this->updated_rows;
    }
    /**
     * @param Collection $collection
     */
    protected $invalid_rows = [];
    protected $duplicate_rows = [];

    public function getInvalidRows()
    {
        return $this->invalid_rows;
    }

    public function getDuplicateRows()
    {
        return $this->duplicate_rows;
    }

    public function collection(Collection $rows)
    {
        $data_to_insert = [];
        $seen_emails = [];
        $emails_to_row = [];

        $this->total_rows += count($rows);

        foreach ($rows as $key => $row) {
            $rowNum = $key + 2;
            if($row['email'] == '' || $row['name'] == '' || $row['password'] == '' ) {
                $this->invalid_rows[] = $rowNum;
                continue;
            }
            if (isset($seen_emails[$row['email']])) {
                $this->duplicate_rows[] = $rowNum;
                continue;
            }
            $seen_emails[$row['email']] = true;
            $emails_to_row[$row['email']] = $rowNum;
            $data_to_insert[] = [
                'email' => $row['email'],
                'name' => $row['name'],
                'password' => $row['password'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Find which emails already exist in DB
        $existing_emails = User::whereIn('email', array_keys($seen_emails))->pluck('email')->toArray();
        $this->updated_rows += count($existing_emails);
        $this->new_rows += count($seen_emails) - count($existing_emails);

        // Use upsert for bulk insert/update based on email
        User::upsert(
            $data_to_insert,
            ['email'],
            ['name', 'password', 'updated_at']
        );

    // No need to return anything; use getInvalidRows()
    }

    /**
     * Define the chunk size (e.g., 1000 rows at a time).
     * @return int
     */
    public function chunkSize(): int
    {
        return 20;
    }
}