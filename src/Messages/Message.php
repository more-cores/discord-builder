<?php

namespace DiscordBuilder\Messages;

use DiscordBuilder\Hydrateable;
use DiscordBuilder\Jsonable;
use DiscordBuilder\Messages\Components\Component;
use DiscordBuilder\Messages\Embed\Embed;

class Message extends Jsonable implements Hydrateable
{
    protected ?string $content;
    protected array $embeds = [];
    protected array $components = [];

    /**
     * @param string|null $content
     * @param Embed[]|null $embeds
     * @param Component[]|null $components
     */
    public function __construct(
        ?string $content = null,
        array $embeds = [],
        array $components = [],
    ) {
        $this->content = $content;
        $this->embeds = $embeds;
        $this->components = $components;
    }

    /**
     * Set the content of a message.  Will override any roles you have mentioned
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function content(): string
    {
        return (string) $this->content;
    }

    public function hasContent(): bool
    {
        return $this->content != null;
    }

    /**
     * Determine if the given message has mentions
     */
    public function hasMentions(): bool
    {
        return preg_match('#<@&.+?>#', $this->content());
    }

    public function mentionedRoleIds(): ?array
    {
        preg_match_all('#<@&(.+?)>#', $this->content(), $matches);

        return isset($matches[1]) ? $matches[1] : null;
    }

    /**
     * Mention the given role in the included message
     */
    public function mention(int $roleId): void
    {
        $this->content .= '<@&'.$roleId.'>';
    }

    public function isMentioned(int $roleId): bool
    {
        if ($this->content == null) {
            return false;
        }

        return str_contains($this->content, '<@&' . $roleId . '>');
    }

    public function addEmbed(Embed $embed): void
    {
        $this->embeds[] = $embed;
    }

    /**
     * @return Embed[]
     */
    public function embeds(): array
    {
        return $this->embeds;
    }

    public function hasEmbeds(): bool
    {
        return count($this->embeds) > 0;
    }

    public function addComponent(Component $component): void
    {
        $this->components[] = $component;
    }

    public function setComponents(array $components): void
    {
        $this->components = $components;
    }

    /**
     * @return Component[]
     */
    public function components(): array
    {
        return $this->components;
    }

    public function hasComponents(): bool
    {
        return count($this->components) > 0;
    }

    public function hydrate(array $array): self
    {
        if (isset($array['content'])) {
            $this->setContent($array['content']);
        }

        // Component hydration is not currently supported - PR's welcome

        if (isset($array['embeds'])) {
            foreach ($array['embeds'] as $embed) {
                if ($embed instanceof Embed) {
                    $this->addEmbed($embed);
                } else {
                    $this->addEmbed((new Embed)->hydrate($embed));
                }
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        $jsonData = [
            'content' => $this->content(),
        ];

        if ($this->hasComponents()) {
            $jsonData['components'] = [];

            foreach ($this->components() as $component) {
                $jsonData['components'][] = $component->jsonSerialize();
            }
        }

        if ($this->hasEmbeds()) {
            $jsonData['embeds'] = [];

            foreach ($this->embeds() as $embed) {
                $jsonData['embeds'][] = $embed->jsonSerialize();
            }
        }

        return $jsonData;
    }
}