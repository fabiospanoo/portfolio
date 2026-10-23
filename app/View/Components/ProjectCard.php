<?php

namespace App\View\Components;

use App\Models\Project;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProjectCard extends Component
{
    /**
     * Hues of the project tag classes, keyed by class name.
     * Kept disjoint from the button hues so tags never look like the button.
     */
    private const TAG_COLORS = [
        'project-tag-0' => '#F14C4C',
        'project-tag-1' => '#F7DF1E',
        'project-tag-2' => '#9CDCFE',
        'project-tag-3' => '#569CD6',
        'project-tag-4' => '#8B949E',
    ];

    /** Language/technology tag => tag class. */
    private const LANGUAGE_COLORS = [
        'blade' => 'project-tag-0',
        'laravel' => 'project-tag-0',
        'livewire' => 'project-tag-0',
        'java' => 'project-tag-1',
        'javascript' => 'project-tag-1',
        'scout' => 'project-tag-1',
        'vite' => 'project-tag-1',
        'python' => 'project-tag-2',
        'tailwind' => 'project-tag-2',
        'docker' => 'project-tag-2',
        'agentic-ai' => 'project-tag-2',
        'php' => 'project-tag-3',
        'bootstrap' => 'project-tag-3',
        'fortify' => 'project-tag-3',
        'mysql' => 'project-tag-3',
        'linux' => 'project-tag-4',
    ];

    private const FALLBACK_TAG_CLASS = 'project-tag-4';

    /** Ordered cycle of the button styles. */
    private const BUTTON_CLASSES = ['primary', 'tertiary', 'secondary', 'quaternary', 'senary', 'quinary'];

    /** Button style => hue. */
    private const BUTTON_COLORS = [
        'primary' => '#B100CD',
        'tertiary' => '#CE9178',
        'secondary' => '#DCDCAA',
        'quaternary' => '#569CD6',
        'senary' => '#6A9955',
        'quinary' => '#9CDCFE',
    ];

    private static int $lastAssignedIdx = -1;

    private static int $lastIndex = -1;

    private ?string $resolvedButtonClass = null;

    public function __construct(
        public readonly Project $project,
        public readonly int $index = 0,
        public readonly ?string $button = null,
    ) {}

    public static function resetCycle(): void
    {
        self::$lastAssignedIdx = -1;
        self::$lastIndex = -1;
    }

    public function tagClass(string $tag): string
    {
        return self::resolveTagClass($tag);
    }

    public static function resolveTagClass(string $tag): string
    {
        return self::LANGUAGE_COLORS[strtolower($tag)] ?? self::FALLBACK_TAG_CLASS;
    }

    /**
     * Button style for this card. Continuously cycles through BUTTON_CLASSES
     * across cards so adjacent cards never share the same button style,
     * while skipping any button hue that collides with the card's tags.
     */
    public function buttonClass(): string
    {
        if ($this->button !== null) {
            return $this->button;
        }

        if ($this->resolvedButtonClass !== null) {
            return $this->resolvedButtonClass;
        }

        $count = count(self::BUTTON_CLASSES);

        if ($this->index === 0 || $this->index <= self::$lastIndex) {
            self::$lastAssignedIdx = ($this->index === 0) ? -1 : ($this->index - 1 + $count) % $count;
        }
        self::$lastIndex = $this->index;

        $tagHues = collect($this->project->tags ?? [])
            ->map(fn (string $tag) => self::TAG_COLORS[$this->tagClass($tag)] ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $idx = (self::$lastAssignedIdx + 1) % $count;
        $attempts = 0;

        while (
            $attempts < $count
            && isset(self::BUTTON_COLORS[self::BUTTON_CLASSES[$idx]])
            && in_array(self::BUTTON_COLORS[self::BUTTON_CLASSES[$idx]], $tagHues, true)
        ) {
            $idx = ($idx + 1) % $count;
            $attempts++;
        }

        self::$lastAssignedIdx = $idx;
        $this->resolvedButtonClass = self::BUTTON_CLASSES[$idx];

        return $this->resolvedButtonClass;
    }

    public function render(): View|Closure|string
    {
        return view('components.project-card');
    }
}
