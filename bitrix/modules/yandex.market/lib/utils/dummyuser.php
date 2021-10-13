<?php

namespace Yandex\Market\Utils;

class DummyUser extends \CUser
{
	public function GetParam($name)
	{
		return null;
	}

	public function SetParam($name, $value)
	{
		// nothing
	}
}