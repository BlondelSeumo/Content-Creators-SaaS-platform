<form>
    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="notification_email_new_sub" name="notification_email_new_sub"
                   {{isset(Auth::user()->settings['notification_email_new_sub']) ? (Auth::user()->settings['notification_email_new_sub'] == 'true' ? 'checked' : '') : false}}>
            <label class="custom-control-label" for="notification_email_new_sub">{{__('New subscription registered')}}</label>
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="notification_email_new_tip" name="notification_email_new_tip"
                {{isset(Auth::user()->settings['notification_email_new_tip']) ? (Auth::user()->settings['notification_email_new_tip'] == 'true' ? 'checked' : '') : false}}>
            <label class="custom-control-label" for="notification_email_new_tip">{{__('Received a tip')}}</label>
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="notification_email_new_message" name="notification_email_new_message"
                   {{isset(Auth::user()->settings['notification_email_new_message']) ? (Auth::user()->settings['notification_email_new_message'] == 'true' ? 'checked' : '') : false}}>
            <label class="custom-control-label" for="notification_email_new_message">{{__('New message received')}}</label>
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="notification_email_new_comment" name="notification_email_new_comment"
                {{isset(Auth::user()->settings['notification_email_new_comment']) ? (Auth::user()->settings['notification_email_new_comment'] == 'true' ? 'checked' : '') : false}}>
            <label class="custom-control-label" for="notification_email_new_comment">{{__('New comment received')}}</label>
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="notification_email_expiring_subs" name="notification_email_expiring_subs"
                   {{isset(Auth::user()->settings['notification_email_expiring_subs']) ? (Auth::user()->settings['notification_email_expiring_subs'] == 'true' ? 'checked' : '') : false}}>
            <label class="custom-control-label" for="notification_email_expiring_subs">{{__('Expiring subscriptions')}}</label>
        </div>
    </div>
    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="notification_email_renewals" name="notification_email_renewals"
                   {{isset(Auth::user()->settings['notification_email_renewals']) ? (Auth::user()->settings['notification_email_renewals'] == 'true' ? 'checked' : '') : false}}>
            <label class="custom-control-label" for="notification_email_renewals">{{__('Upcoming renewals')}}</label>
        </div>
    </div>
</form>
