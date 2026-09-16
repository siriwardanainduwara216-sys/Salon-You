<?php


function calculate_loyalty_tier($points) {
    // Below 100 points: no rank/reward unlocked yet
    if ($points < 100) {
        return [
            'tier_name'       => 'Bronze',
            'tier_icon'       => 'fa-medal',
            'tier_color'      => '#a97142', // bronze color
            'discount'        => 0,
            'points_to_next'  => 100 - $points,
            'next_tier_name'  => 'Silver',
        ];
    }

    // From 100 points onward, a new tier unlocks every 50 points.
    // tier_index: 1 = Silver (50% off), 2 = Gold (25%), 3 = Platinum (25%), 4+ = Diamond Lv.N (25%)
    $tier_index = floor(($points - 100) / 50) + 1;
    $next_threshold = 100 + ($tier_index * 50);
    $points_to_next = $next_threshold - $points;

    $tiers = [
        1 => ['name' => 'Silver',   'icon' => 'fa-medal',  'color' => '#c0c0c0', 'discount' => 50],
        2 => ['name' => 'Gold',     'icon' => 'fa-trophy', 'color' => '#d4af37', 'discount' => 25],
        3 => ['name' => 'Platinum', 'icon' => 'fa-crown',  'color' => '#8b5cf6', 'discount' => 25],
        4 => ['name' => 'Diamond',  'icon' => 'fa-gem',    'color' => '#3b82f6', 'discount' => 25],
    ];

    if (isset($tiers[$tier_index])) {
        $current = $tiers[$tier_index];
        $tier_name = $current['name'];
    } else {
        // Beyond Diamond: keep going up as "Diamond Elite Lv.N"
        $elite_level = $tier_index - 4;
        $current = ['icon' => 'fa-gem', 'color' => '#3b82f6', 'discount' => 25];
        $tier_name = 'Diamond Elite Lv.' . $elite_level;
    }

    // Figure out what the NEXT tier will be called, for the progress display
    $next_index = $tier_index + 1;
    if (isset($tiers[$next_index])) {
        $next_tier_name = $tiers[$next_index]['name'];
    } else {
        $next_elite = $next_index - 4;
        $next_tier_name = $next_elite <= 1 ? 'Diamond' : 'Diamond Elite Lv.' . $next_elite;
    }

    return [
        'tier_name'      => $tier_name,
        'tier_icon'      => $current['icon'],
        'tier_color'     => $current['color'],
        'discount'       => $current['discount'],
        'points_to_next' => $points_to_next,
        'next_tier_name' => $next_tier_name,
    ];
}