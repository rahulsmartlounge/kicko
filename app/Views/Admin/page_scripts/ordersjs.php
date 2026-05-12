<script>
    var baseUrl = "<?= base_url() ?>";
    var csrfTokenName = "<?= csrf_token() ?>";
    var csrfHash = "<?= csrf_hash() ?>";

    var table = $('#orderList').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [[6, 'desc']],
        ajax: {
            url: baseUrl + "admin/orders/List",
            type: "POST",
            data: function (d) {
                d[csrfTokenName] = csrfHash;
            }
        },
     columns: [
    {
        data: null,
        render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        },
        orderable: false,
        searchable: false
    },
    {
    data: 'cust_Name',
    render: function (data, type, row) {
        if (!data) return 'N/A';

        const fullName = data;
        const displayName = data.length > 25 ? data.substring(0, 25) + '...' : data;
 return '<a href="' + baseUrl + 'admin/orders/view/' + row.od_Id + '" style="text-decoration: none;">' + displayName + '</a>';
    }
},

    { data: 'add_Email' },
    { data: 'add_Phone' },
    { data: 'pr_Code' },
    { data: 'od_Quantity' },
    { data: 'od_createdon' },
    { data: 'od_Status' },
    {
        data: 'actions',
        orderable: false,
        searchable: false
        // remove render.html() here
    }
]


    });

</script>