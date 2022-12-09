<?php

namespace AppBundle\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;

/**
 * Class TraceLogFormatter
 *
 * Custom log formatter for 'queue' log handler.
 *
 * @package AppBundle\Monolog\Formatter
 */
class TraceLogFormatter extends LineFormatter
{
    /**
     * Formats a log record.
     *
     * @param array $record A record to format.
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $trace = [];
        if (isset($record['context']['trace'])) {
            $trace = $record['context']['trace'];
            unset($record['context']['trace']);
        }

        $output = parent::format($record);

        if (count($trace) > 0) {
            $output .= "\n  Trace:\n\n";
            $idx = 0;
            foreach ($trace as $step) {
                $output .= sprintf(
                    "    #%d %s::%s()\n",
                    $idx++,
                    isset($step['file']) ? $step['file'].':'.$step['line'] : $step['class'],
                    $step['function']
                );

                $args = isset($step['args']) ? $step['args'] : [];
                if (count($args) > 0) {
                    $output .= "    Arguments:\n";
                    foreach ($args as $arg) {
                        $output .= "      {$this->processArgument($arg)}\n";
                    }
                }

                $output .= "\n";
            }
        }

        return $output;
    }

    /**
     * @param mixed $argument Trace arguments.
     *
     * @return string
     */
    private function processArgument($argument)
    {
        switch (true) {
            case is_array($argument):
                $result = '';

                foreach ($argument as $key => $item) {
                    $result .= "{$key} => {$this->processArgument($item)},";
                }

                return "[{$result}]";

            case is_object($argument):
                return get_class($argument);

            default:
                return var_export($argument, true);
        }
    }
}
