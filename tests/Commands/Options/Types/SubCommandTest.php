<?php

namespace DiscordBuilder\Commands\Options;

use DiscordBuilder\Commands\HasCommandOptions;
use DiscordBuilder\Commands\Options\Types\SubCommandOption;
use PHPUnit\Framework\TestCase;

class SubCommandTest extends TestCase
{
    /** @test */
    public function serializesOptions()
    {
        $optionType = time();
        $option = new Option($optionType);
        $command = new SubCommandOption();

        $this->assertFalse($command->hasOptions());

        $command->addOption($option);

        $this->assertTrue($command->hasOptions());

        $this->assertEquals($optionType, $command->options()[0]->type());

        $json = $command->jsonSerialize();

        $this->assertArrayHasKey('options', $json);
        $this->assertEquals($optionType, $json['options'][0]['type']);
    }
}
