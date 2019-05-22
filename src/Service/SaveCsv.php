<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 21/05/2019
 * Time: 14:34
 */

namespace App\Service;

use Psr\Log\LoggerInterface;

class SaveCsv
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function saveFile($fileName, $dataTab)
    {
        try{

            $path = '/';
            $delimiter = ',';
            $enclosure = '"';
            $escape_char = "\n";

            $file = fopen($path, 'w+');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach($dataTab as $data)
            {
                fputcsv($file, $data, $delimiter, $enclosure, $escape_char);
            }

            return $file;

        }
        catch (\Exception $e)
        {
            $this->logger->info($e);
        }
    }

}