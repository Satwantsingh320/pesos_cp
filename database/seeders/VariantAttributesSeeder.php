<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;

class VariantAttributesSeeder extends Seeder
{
    public function run()
    {
        // Size Attribute
        $size = VariantAttribute::create([
            'name' => 'size',
            'display_name' => 'Size',
            'type' => 'select',
            'status' => 1
        ]);

        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        foreach ($sizes as $index => $sizeValue) {
            VariantAttributeValue::create([
                'attribute_id' => $size->id,
                'value' => $sizeValue,
                'position' => $index
            ]);
        }

        // Color Attribute
        $color = VariantAttribute::create([
            'name' => 'color',
            'display_name' => 'Color',
            'type' => 'color',
            'status' => 1
        ]);

        $colors = [
            ['value' => 'Red', 'color_code' => '#FF0000'],
            ['value' => 'Blue', 'color_code' => '#0000FF'],
            ['value' => 'Green', 'color_code' => '#00FF00'],
            ['value' => 'Black', 'color_code' => '#000000'],
            ['value' => 'White', 'color_code' => '#FFFFFF'],
        ];

        foreach ($colors as $index => $colorData) {
            VariantAttributeValue::create([
                'attribute_id' => $color->id,
                'value' => $colorData['value'],
                'color_code' => $colorData['color_code'],
                'position' => $index
            ]);
        }
    }
}
