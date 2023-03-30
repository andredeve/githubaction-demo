<?php

namespace App\Util\Tesseract;

class TesseractOCR
{

    public $command;

    public function __construct($image = null, $command = null)
    {
        $this->command = $command ?: new Command;
        $this->image("$image");
    }


    public function pdfToText($file_pdf)
    {
        $path = pathinfo($file_pdf);
        $file_txt = $path['dirname'] . "/" . $path['filename'] . '.txt';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command_read_pdf = "";
        } else {
            $command_read_pdf = "sudo";
        }
        $command_read_pdf .= " pdftotext $file_pdf $file_txt 2>&1";
        FriendlyErrors::checkExecutablePresence('pdftotext');
        exec($command_read_pdf, $stdout);
        // FriendlyErrors::checkCommandExecutionConvertText($command_read_pdf, $this->command, $stdout);
        if (is_file($file_txt)) {
            $text = file_get_contents($file_txt);
            unlink($file_txt);
            return trim($text, " \t\n\r\0\x0A\x0B\x0C");
        } else {
            return null;
        }

    }

    public function run()
    {
        FriendlyErrors::checkExecutablePresence($this->command->executable);
        FriendlyErrors::checkImagePath($this->command->image);
        exec("{$this->command} 2>&1", $stdout);
        FriendlyErrors::checkCommandExecution($this->command, $stdout);
        return $this->pdfToText($this->command->getOutputFile() . '.pdf');

    }


    public function image($image)
    {
        $this->command->image = $image;
        return $this;
    }

    public function executable($executable)
    {
        $this->command->executable = $executable;
        return $this;
    }

    public function outPutFile($outputFile)
    {
        $this->command->outputFile = $outputFile;
        return $this;
    }

    public function configFile($configFile)
    {
        $this->command->configFile = $configFile;
        return $this;
    }

    public function threadLimit($limit)
    {
        $this->command->threadLimit = $limit;
        return $this;
    }

    // @deprecated
    public function format($fmt)
    {
        return $this->configFile($fmt);
    }

    public function whitelist()
    {
        $concat = function ($arg) {
            return is_array($arg) ? join('', $arg) : $arg;
        };
        $whitelist = join('', array_map($concat, func_get_args()));
        $this->command->options[] = Option::config('tessedit_char_whitelist', $whitelist);
        return $this;
    }

    public function version()
    {
        return $this->command->getTesseractVersion();
    }

    public function availableLanguages()
    {
        return $this->command->getAvailableLanguages();
    }

    public function __call($method, $args)
    {
        if ($this->isConfigFile($method))
            return $this->configFile($method);
        if ($this->isOption($method)) {
            $option = $this->getOptionClassName() . '::' . $method;
            $this->command->options[] = call_user_func_array($option, $args);
            return $this;
        }
        $arg = empty($args) ? null : $args[0];
        $this->command->options[] = Option::config($method, $arg);
        return $this;
    }

    private function isConfigFile($name)
    {
        return in_array($name, ['digits', 'hocr', 'pdf', 'quiet', 'tsv', 'txt']);
    }

    private function isOption($name)
    {
        return in_array($name, get_class_methods($this->getOptionClassName()));
    }

    private function getOptionClassName()
    {
        return __NAMESPACE__ . '\\Option';
    }

}
