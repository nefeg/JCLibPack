<?php

namespace Aimchat\JCLibPack;

use Exception;

/**
 * Class JCCmd
 *
 * @package Aimchat\JCLibPack
 */
class JCCmd
{
	/**
	 * @var string
	 */
	private $cmd;
	/**
	 * @var string
	 */
	private $env;
	/**
	 * @var array
	 */
	private $arguments;
	/**
	 * @var array
	 */
	private $options;
    /**
     * @var array
     */
    private $noneValueOptions;

    /**
     * JellyCmd constructor.
     *
     * @param string $cmd
     * @param string $env
     * @param array $arguments
     * @param array $options
     * @param array $noneValueOptions
     */
	public function __construct(string $cmd, string $env, array $arguments = [], array $options = [], array $noneValueOptions = []){

		$this->cmd              = $cmd;
		$this->env              = $env;
		$this->arguments        = $arguments;
		$this->options          = $options;
		$this->noneValueOptions = $noneValueOptions;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function __toString() :string{
		return $this->compile();
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function compile() :string{

		return JCCmdLib::compileCmd(
			$this->getCmd(),
			$this->getEnv(),
			$this->getArguments(),
			$this->getOptions(),
            $this->getNoneValueOptions()
		);
	}

	/**
	 * @return string
	 */
	public function getCmd(): string {
		return $this->cmd;
	}

	/**
	 * @return string
	 */
	public function getEnv(): string {
		return $this->env;
	}

	/**
	 * @return array
	 */
	public function getArguments(): array {
		return $this->arguments;
	}

	/**
	 * @param array $arguments
	 * @return JCCmd
	 */
	public function setArguments(array $arguments) :JCCmd{
		$this->arguments = $arguments;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array {
		return $this->options;
	}

	/**
	 * @param array $options
	 * @return JCCmd
	 */
	public function setOptions(array $options) :JCCmd{
		$this->options = $options;

		return $this;
	}

    /**
     * @return array
     */
    public function getNoneValueOptions(): array {
        return $this->noneValueOptions;
    }

    /**
     * @param array $noneValueOptions
     * @return JCCmd
     */
    public function setNoneValueOptions($noneValueOptions) :JCCmd {
        $this->noneValueOptions = $noneValueOptions;
        return $this;
    }
}