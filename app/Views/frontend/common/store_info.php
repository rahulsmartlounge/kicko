<div class="col-md-3">
    <h4>Store Information</h4>
    <ul>
        <li style="display:flex;"><i class="bi bi-geo-alt-fill" ></i>
            Zakhi Designs Store<br />
             16/541P Muppathadam, Near Govt: GHS School Aluva, Ernakulam
        </li>
        <li><i class="bi bi-telephone-fill"></i>
            <?= esc( $admin_user['us_Phone']) ?>
        </li>
        <li class="gmail"><i class="bi bi-envelope-fill"></i>
            <?= esc(strtolower($admin_user['us_Email'])) ?>
        </li>
    </ul>
</div>
