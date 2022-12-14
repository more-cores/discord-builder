<?php

namespace DiscordCommands\Messages\Components\Types\SelectMenu;

use DiscordCommands\Messages\Components\Types\SelectMenu;

class MentionableSelectMenu extends SelectMenu
{
    public const TYPE = 7;

    public function __construct(
        string $id,
    ) {
        parent::__construct(
            type: self::TYPE,
            id: $id
        );
    }
}