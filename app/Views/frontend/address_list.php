
<?php foreach ($addresses as $address): ?>
    <?php
        $text = "{$address['add_BuldingNo']}, {$address['add_Street']}\n{$address['add_Landmark']}\n{$address['add_City']}, {$address['add_State']}\n{$address['add_Pincode']}\n{$address['add_Phone']}";
    ?>
    <div class="border p-2 mb-2">
        <pre><?= esc($text); ?></pre>
        <button type="button" class="btn btn-sm btn-primary select-address-btn" data-address="<?= esc($text, 'js'); ?>">Select</button>
    </div>
<?php endforeach; ?>

<button class="btn btn-link" id="showNewAddressForm">+ Add New Address</button>
