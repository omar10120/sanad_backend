{{-- Pro Version Feature Modal --}}
<div id="proModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="proModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border: 2px solid #f0ad4e; border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #f0ad4e 0%, #ec971f 100%); border: none;">
                <h5 class="modal-title text-white" id="proModalLabel">
                    <i class="fas fa-crown mr-2"></i>
                    {{ trans('main_trans.Pro_Feature') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="{{ trans('main_trans.Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size: 60px; color: #f0ad4e; margin-bottom: 20px;">
                    <i class="fas fa-crown"></i>
                </div>
                <h4 class="mb-3">{{ trans('main_trans.Pro_Feature_Title') }}</h4>
                <p class="text-muted">{{ trans('main_trans.Pro_Feature_Message') }}</p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
function showProModal(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    $('#proModal').modal('show');
}
</script>
