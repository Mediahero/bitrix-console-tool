<?php

namespace BitrixTool;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class ClipboardStream
{
    private $process = null;
    private $stream = null;

    public function __construct()
    {
        $this->process = proc_open('xclip -sel clip', array(array("pipe", "r")), $pipes);
        $this->stream = new StreamOutput($pipes[0], OutputInterface::VERBOSITY_NORMAL, false);
    }

    public function __destruct() {
        proc_close($this->process);
    }

    public function writeln($messages, $type = OutputInterface::OUTPUT_NORMAL)
    {
        $this->write($messages, true, $type);
    }

    public function write($messages, $newline = false, $type = OutputInterface::OUTPUT_NORMAL)
    {
        $this->stream->write($messages, $newline, $type);
    }
}