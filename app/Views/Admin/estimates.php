<div class="pcoded-content">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Estimates</h5>
                        <p class="m-b-0">All generated proposals &amp; estimates</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('admin/dashboard') ?>"><i class="fa fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">Estimates</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-block">
                                    <div class="card">
                                        <div class="card-block table-border-style">
                                            <div class="table-responsive">
                                                <table class="table table-hover" id="estimateList">
                                                    <thead>
                                                        <tr>
                                                            <th></th><!-- expand toggle -->
                                                            <th>Sl.No</th>
                                                            <th>Customer Name</th>
                                                            <th>Phone</th>
                                                            <th>Email</th>
                                                            <th>Project No.</th>
                                                            <th>Date</th>
                                                            <th>Grand Total</th>
                                                            <th>Status</th>
                                                            <th>Created On</th>
                                                            <th class="text-center">PDF</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dt-expand-btn {
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
    color: #5b6be8;
    user-select: none;
    display: inline-block;
    width: 24px;
    text-align: center;
    line-height: 1;
}
.dt-expand-btn.open { color: #e85b5b; }
.child-items-table { margin: 6px 0 6px 30px; width: calc(100% - 40px); }
.child-items-table thead th { background: #f4f4f4; font-size: 12px; }
.child-items-table td, .child-items-table th { font-size: 12px; padding: 4px 8px !important; }
.child-row-category td { background: #e9ecef; font-weight: bold; }
.child-loading { padding: 10px 30px; color: #888; font-style: italic; }
</style>
