<?php

namespace App\Jobs;

use App\Rows;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RowsImport implements ToModel, WithChunkReading, ShouldQueue, WithHeadingRow, WithCalculatedFormulas
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RemembersRowNumber;

    protected $unique_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $unique_file_name = false ) {
        $this->unique_name = $unique_file_name;
    }

    public function model( array $row ) {
        $currentRowNumber = $this->getRowNumber();
        if ( $this->unique_name ) {
            // Пишем прогресс парсинга в редис
            $redis = Redis::connection( 'default' );
            $redis->hset( 'parse_results', $this->unique_name, json_encode( [ 'current_row_number' => $currentRowNumber ] ) );
        }

        return new Rows( [
            'id_row' => $row[ 'id' ],
            'name_row' => $row[ 'name' ],
            'date_row' => $row[ 'date' ],
        ] );
    }

    public function chunkSize(): int {
        return 1000;
    }
}
