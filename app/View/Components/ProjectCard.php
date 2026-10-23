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
        'project-tag-laravel' => '#FF4438',
        'project-tag-php' => '#8892BF',
        'project-tag-blade' => '#F06529',
        'project-tag-livewire' => '#FB70A9',
        'project-tag-fortify' => '#34D399',
        'project-tag-bootstrap' => '#A78BFA',
        'project-tag-tailwind' => '#38BDF8',
        'project-tag-scout' => '#FBBF24',
        'project-tag-java' => '#FB923C',
        'project-tag-python' => '#60A5FA',
        'project-tag-javascript' => '#FACC15',
        'project-tag-mysql' => '#2DD4BF',
        'project-tag-docker' => '#38BDF8',
        'project-tag-vite' => '#C084FC',
        'project-tag-agentic-ai' => '#818CF8',
        'project-tag-linux' => '#CBD5E1',
        'project-tag-default' => '#8B949E',
        // Legacy numeric classes
        'project-tag-0' => '#FF4438',
        'project-tag-1' => '#FACC15',
        'project-tag-2' => '#38BDF8',
        'project-tag-3' => '#8892BF',
        'project-tag-4' => '#8B949E',
    ];

    /** Language/technology tag => tag class. */
    private const LANGUAGE_COLORS = [
        'laravel' => 'project-tag-laravel',
        'php' => 'project-tag-php',
        'blade' => 'project-tag-blade',
        'livewire' => 'project-tag-livewire',
        'fortify' => 'project-tag-fortify',
        'bootstrap' => 'project-tag-bootstrap',
        'tailwind' => 'project-tag-tailwind',
        'scout' => 'project-tag-scout',
        'java' => 'project-tag-java',
        'python' => 'project-tag-python',
        'javascript' => 'project-tag-javascript',
        'mysql' => 'project-tag-mysql',
        'docker' => 'project-tag-docker',
        'vite' => 'project-tag-vite',
        'agentic-ai' => 'project-tag-agentic-ai',
        'linux' => 'project-tag-linux',
    ];

    private const FALLBACK_TAG_CLASS = 'project-tag-default';

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
