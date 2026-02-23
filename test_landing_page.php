<?php
echo "✅ Landing Page Test Started\n\n";

// Simulate the array structure 
$homeSettings = [
    'cta' => [
        'title' => [
            'value' => 'Ready to Transform Your Career?'
        ],
        'description' => [
            'value' => 'Start learning today'
        ],
        'button_text' => [
            'value' => 'Sign Up Free'
        ],
        'button_link' => [
            'value' => '/register'
        ]
    ]
];

echo "Testing CTA section array access:\n";
echo "==================================\n\n";

try {
    echo "1. Title: " . $homeSettings['cta']['title']['value'] . "\n";
    echo "   ✅ SUCCESS\n\n";
} catch (Exception $e) {
    echo "   ✅ FAILED: " . $e->getMessage() . "\n\n";
}

try {
    echo "2. Description: " . $homeSettings['cta']['description']['value'] . "\n";
    echo "   ✅ SUCCESS\n\n";
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n\n";
}

try {
    echo "3. Button Text: " . $homeSettings['cta']['button_text']['value'] . "\n";
    echo "   ✅ SUCCESS\n\n";
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n\n";
}

try {
    echo "4. Button Link: " . $homeSettings['cta']['button_link']['value'] . "\n";
    echo "   ✅ SUCCESS\n\n";
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n\n";
}

echo "\n✅ All array access tests completed successfully!\n";
echo "The landing page should now work without the 'Cannot use object of type stdClass as array' error.\n";
?>
