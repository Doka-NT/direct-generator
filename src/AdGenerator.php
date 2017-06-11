<?php

namespace skobka\dg;

class AdGenerator
{
	const KEYWORD_MARKER = '[key]';

	/**
	 * @var Parser
	 */
	private $parser;
	/**
	 * @var \SplFileObject
	 */
	private $output;
	/**
	 * @var string
	 */
	private $cellDelimiter = "\t\t";
	/**
	 * @var string
	 */
	private $rowDelimiter = "\n";

	/**
	 * AdGenerator constructor.
	 *
	 * @param Parser $parser
	 * @param string $output
	 */
	public function __construct(Parser $parser, string $output)
	{
		$this->parser = $parser;
		$this->output = new \SplFileObject($output,'w');

		$this->parser->parse();
	}

	/**
	 * Генерирует наборы объявлений
	 */
	public function generate()
	{
		foreach ($this->parser->getKeywords() as $keyword) {
			foreach ($this->parser->getTitles() as $title) {
				$title = $this->replaceKeys($title, $keyword);
				foreach ($this->parser->getTexts() as $text) {
					$text = $this->replaceKeys($text, $keyword);

					$str = sprintf(
						"%s%s%s%s%s%s",
						$keyword,
						$this->cellDelimiter,
						$title,
						$this->cellDelimiter,
						$text,
						$this->rowDelimiter
					);

					$this->output->fwrite($str);
				}
			}
		}
	}

	/**
	 * @param string $cellDelimiter
	 */
	public function setCellDelimiter(string $cellDelimiter)
	{
		$this->cellDelimiter = $cellDelimiter;
	}

	/**
	 * @param string $rowDelimiter
	 */
	public function setRowDelimiter(string $rowDelimiter)
	{
		$this->rowDelimiter = $rowDelimiter;
	}

	/**
	 * @param string $input
	 * @param string $replacement
	 *
	 * @return string
	 */
	private function replaceKeys(string $input, string $replacement): string
	{
		$replacement = preg_replace('/\+|-/', '', $replacement);

		$result = str_replace('[Key]', mb_convert_case($replacement, MB_CASE_TITLE, 'UTF-8'), $input);
		$result = str_replace('[key]', mb_convert_case($replacement, MB_CASE_LOWER, 'UTF-8'), $result);

		return $result;
	}
}
