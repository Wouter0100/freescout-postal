<div data-in-protocol="{{ $incomingHttp }}" style="display: none;">
    <div>
        <div class="form-group">
            <label for="imap_sent_folder" class="col-sm-2 control-label">{{ __('HTTP endpoint') }}</label>

            <div class="col-sm-6">
                <input type="text" class="form-control input-sized" readonly value="{{$mailbox->getPostalEndpoint()}}">
                <div class="form-help">{!! __("This URL should be used as the HTTP endpoint for within Postal.") !!}</div>
            </div>
        </div>
        <hr/>
    </div>
</div>
