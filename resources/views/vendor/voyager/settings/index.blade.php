@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.__('voyager::generic.settings'))

@section('css')
    <link href="{{ asset('css/admin-settings.css') }}" rel="stylesheet">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-settings"></i> {{ __('voyager::generic.settings') }}
    </h1>
@stop

@section('content')
    <div class="container-fluid">
        @include('voyager::alerts')
        @if(config('voyager.show_dev_tips'))
        <div class="alert alert-info">
            <strong>{{ __('voyager::generic.how_to_use') }}:</strong>
            <p>{{ __('voyager::settings.usage_help') }} <code>getSetting('group.key')</code></p>
        </div>
        @endif
    </div>

    <div class="page-content settings container-fluid">
        <form action="{{ route('voyager.settings.update') }}" method="POST" enctype="multipart/form-data" class="save-settings-form">
            {{ method_field("PUT") }}
            {{ csrf_field() }}
            <input type="hidden" name="setting_tab" class="setting_tab" value="{{ $active }}" />
            <div class="panel">

                <div class="page-content settings container-fluid">
                    <ul class="nav nav-tabs">
                        @foreach($settings as $group => $setting)
                            <li @if($group == $active) class="active" @endif>
                                <a data-toggle="tab" href="#{{ \Illuminate\Support\Str::slug($group) }}">{{ $group }}</a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content">

                        <div class="tab-additional-info">
                            <div class="alert alert-info alert-dismissible mb-1">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <div class="info-label"><div class="icon voyager-dollar"></div>The payment system requires cronjobs so you can easily setup them by using the following line:</div>
                                <ul class="mb-1">
                                    <li><code>* * * * *  root cd {{base_path()}} && php artisan schedule:run >> /dev/null 2>&1</code></li>
                                </ul>
                            </div>

                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <div class="info-label"> <div class="icon voyager-info-circled"></div> In order to be able to receive payment updates from the payment providers, please use these webhooks endpoints:</div>
                                <ul>
                                    <li><code>{{route('paypal.payment.update')}}</code></li>
                                    <li><code>{{route('stripe.payment.update')}}</code></li>
                                    <li><code>{{route('coinbase.payment.update')}}</code></li>
                                </ul>
                            </div>

                        </div>

                        {{-- Payments additional info --}}
                        <div class="tab-additional-info">

                        </div>

                        @foreach($settings as $group => $group_settings)
                        <div id="{{ \Illuminate\Support\Str::slug($group) }}" class="tab-pane fade in @if($group == $active) active @endif">
                            @foreach($group_settings as $setting)
                            <div class="panel-heading setting-row setting-{{$setting->key}}" data-settingkey={{$setting->key}}>
                                <h3 class="panel-title">
                                    {{ $setting->display_name }} @if(config('voyager.show_dev_tips'))<code>getSetting('{{ $setting->key }}')</code>@endif
                                </h3>
                                @if((env('APP_ENV') != 'production' && env('APP_DEBUG') == true))
                                    <div class="panel-actions">
                                        <a href="{{ route('voyager.settings.move_up', $setting->id) }}">
                                            <i class="sort-icons voyager-sort-asc"></i>
                                        </a>
                                        <a href="{{ route('voyager.settings.move_down', $setting->id) }}">
                                            <i class="sort-icons voyager-sort-desc"></i>
                                        </a>
                                        @can('delete', Voyager::model('Setting'))
                                        <i class="voyager-trash"
                                           data-id="{{ $setting->id }}"
                                           data-display-key="{{ $setting->key }}"
                                           data-display-name="{{ $setting->display_name }}"></i>
                                        @endcan
                                    </div>
                                @endif
                            </div>

                            <div class="panel-body no-padding-left-right setting-row setting-{{$setting->key}}" data-settingkey={{$setting->key}}>
                                <div class="{{(env('APP_ENV') != 'production' && env('APP_DEBUG') == true) ? 'col-md-10' : 'col-md-12'}} no-padding-left-right">
                                    @if ($setting->type == "text")
                                        <input type="text" class="form-control" name="{{ $setting->key }}" value="{{ $setting->value }}">
                                    @elseif($setting->type == "text_area")
                                        <textarea class="form-control" name="{{ $setting->key }}">{{ $setting->value ?? '' }}</textarea>
                                    @elseif($setting->type == "rich_text_box")
                                        <textarea class="form-control richTextBox" name="{{ $setting->key }}">{{ $setting->value ?? '' }}</textarea>
                                    @elseif($setting->type == "code_editor")
                                        <?php $options = json_decode($setting->details); ?>
                                        <div id="{{ $setting->key }}" data-theme="{{ @$options->theme }}" data-language="{{ @$options->language }}" class="ace_editor min_height_400" name="{{ $setting->key }}">{{ $setting->value ?? '' }}</div>
                                        <textarea name="{{ $setting->key }}" id="{{ $setting->key }}_textarea" class="hidden">{{ $setting->value ?? '' }}</textarea>
                                    @elseif($setting->type == "image" || $setting->type == "file")
                                        @if(isset( $setting->value ) && !empty( $setting->value ) && Storage::disk(config('voyager.storage.disk'))->exists($setting->value))
                                            <div class="img_settings_container">
                                                <a href="{{ route('voyager.settings.delete_value', $setting->id) }}" class="voyager-x delete_value"></a>
                                                <img src="{{ Storage::disk(config('voyager.storage.disk'))->url($setting->value) }}" class="setting-value-image">
                                            </div>
                                            <div class="clearfix"></div>
                                        @elseif($setting->type == "file" && isset( $setting->value ))
                                            @if(json_decode($setting->value) !== null)
                                                @foreach(json_decode($setting->value) as $file)
                                                  <div class="fileType">
                                                    <a class="fileType" target="_blank" href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) }}">
                                                      {{ $file->original_name }}
                                                    </a>
                                                    <a href="{{ route('voyager.settings.delete_value', $setting->id) }}" class="voyager-x delete_value"></a>
                                                 </div>
                                                @endforeach
                                            @endif
                                        @endif
                                        <input type="file" name="{{ $setting->key }}">
                                    @elseif($setting->type == "select_dropdown")
                                        <?php $options = json_decode($setting->details); ?>
                                        <?php $selected_value = (isset($setting->value) && !empty($setting->value)) ? $setting->value : NULL; ?>
                                        <select class="form-control" name="{{ $setting->key }}">
                                            <?php $default = (isset($options->default)) ? $options->default : NULL; ?>
                                            @if(isset($options->options))
                                                @foreach($options->options as $index => $option)
                                                    <option value="{{ $index }}" @if($default == $index && $selected_value === NULL) selected="selected" @endif @if($selected_value == $index) selected="selected" @endif>{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                    @elseif($setting->type == "radio_btn")
                                        <?php $options = json_decode($setting->details); ?>
                                        <?php $selected_value = (isset($setting->value) && !empty($setting->value)) ? $setting->value : NULL; ?>
                                        <?php $default = (isset($options->default)) ? $options->default : NULL; ?>
                                        <ul class="radio">
                                            @if(isset($options->options))
                                                @foreach($options->options as $index => $option)
                                                    <li>
                                                        <input type="radio" id="option-{{ $index }}" name="{{ $setting->key }}"
                                                               value="{{ $index }}" @if($default == $index && $selected_value === NULL) checked @endif @if($selected_value == $index) checked @endif>
                                                        <label for="option-{{ $index }}">{{ $option }}</label>
                                                        <div class="check"></div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    @elseif($setting->type == "checkbox")
                                        <?php $options = json_decode($setting->details); ?>
                                        <?php $checked = (isset($setting->value) && $setting->value == 1) ? true : false; ?>
                                        @if (isset($options->on) && isset($options->off))
                                            <input type="checkbox" name="{{ $setting->key }}" class="toggleswitch" @if($checked) checked @endif data-on="{{ $options->on }}" data-off="{{ $options->off }}">
                                        @else
                                            <input type="checkbox" name="{{ $setting->key }}" @if($checked) checked @endif class="toggleswitch">
                                        @endif
                                    @endif
                                </div>
                                <div class="{{(env('APP_ENV') != 'production' && env('APP_DEBUG') == true) ? 'col-md-2 no-padding-left-right' : 'col-md-2 no-padding-left-right d-none'}}">
                                    <select class="form-control group_select" name="{{ $setting->key }}_group">
                                        @foreach($groups as $group)
                                        <option value="{{ $group }}" {!! $setting->group == $group ? 'selected' : '' !!}>{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                                <?php
                                $settingDetails = json_decode($setting->details);
                                $hasDescription = false;
                                if(isset($settingDetails->description)){
                                    $hasDescription = true;
                                }
                                ?>
                                @if($hasDescription)
                                    <div class="admin-setting-description">
                                        <code>
                                            {{$settingDetails->description}}
                                        </code>
                                    </div>
                                @endif
                            @if(!$loop->last)
                                <hr class="setting-row setting-{{$setting->key}}" data-settingkey={{$setting->key}}>
                            @endif
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
            <button type="submit" class="btn btn-primary pull-right">{{ __('voyager::settings.save') }}</button>
        </form>

        <div class="clearfix"></div>

        @can('add', Voyager::model('Setting'))
            @if((env('APP_ENV') != 'production' && env('APP_DEBUG') == true))
                <div class="panel mt-10">
                    <div class="panel-heading new-setting">
                        <hr>
                        <h3 class="panel-title"><i class="voyager-plus"></i> {{ __('voyager::settings.new') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('voyager.settings.store') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" name="setting_tab" class="setting_tab" value="{{ $active }}" />
                            <div class="col-md-3">
                                <label for="display_name">{{ __('voyager::generic.name') }}</label>
                                <input type="text" class="form-control" name="display_name" placeholder="{{ __('voyager::settings.help_name') }}" required="required">
                            </div>
                            <div class="col-md-3">
                                <label for="key">{{ __('voyager::generic.key') }}</label>
                                <input type="text" class="form-control" name="key" placeholder="{{ __('voyager::settings.help_key') }}" required="required">
                            </div>
                            <div class="col-md-3">
                                <label for="type">{{ __('voyager::generic.type') }}</label>
                                <select name="type" class="form-control" required="required">
                                    <option value="">{{ __('voyager::generic.choose_type') }}</option>
                                    <option value="text">{{ __('voyager::form.type_textbox') }}</option>
                                    <option value="text_area">{{ __('voyager::form.type_textarea') }}</option>
                                    <option value="rich_text_box">{{ __('voyager::form.type_richtextbox') }}</option>
                                    <option value="code_editor">{{ __('voyager::form.type_codeeditor') }}</option>
                                    <option value="checkbox">{{ __('voyager::form.type_checkbox') }}</option>
                                    <option value="radio_btn">{{ __('voyager::form.type_radiobutton') }}</option>
                                    <option value="select_dropdown">{{ __('voyager::form.type_selectdropdown') }}</option>
                                    <option value="file">{{ __('voyager::form.type_file') }}</option>
                                    <option value="image">{{ __('voyager::form.type_image') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="group">{{ __('voyager::settings.group') }}</label>
                                <select class="form-control group_select group_select_new" name="group">
                                    @foreach($groups as $group)
                                        <option value="{{ $group }}">{{ $group }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <a id="toggle_options"><i class="voyager-double-down"></i> {{ mb_strtoupper(__('voyager::generic.options')) }}</a>
                                <div class="new-settings-options">
                                    <label for="options">{{ __('voyager::generic.options') }}
                                        <small>{{ __('voyager::settings.help_option') }}</small>
                                    </label>
                                    <div id="options_editor" class="form-control min_height_200" data-language="json"></div>
                                    <textarea id="options_textarea" name="details" class="hidden"></textarea>
                                    <div id="valid_options" class="alert-success alert d-none">{{ __('voyager::json.valid') }}</div>
                                    <div id="invalid_options" class="alert-danger alert d-none">{{ __('voyager::json.invalid') }}</div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <button type="submit" class="btn btn-primary pull-right new-setting-btn">
                                <i class="voyager-plus"></i> {{ __('voyager::settings.add_new') }}
                            </button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            @endif
        @endcan
    </div>

    @can('delete', Voyager::model('Setting'))
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">
                        <i class="voyager-trash"></i> {!! __('voyager::settings.delete_question', ['setting' => '<span id="delete_setting_title"></span>']) !!}
                    </h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::settings.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endcan

@stop

@section('javascript')
    <script>
        $('document').ready(function () {
            $('#toggle_options').on('click', function () {
                $('.new-settings-options').toggle();
                if ($('#toggle_options .voyager-double-down').length) {
                    $('#toggle_options .voyager-double-down').removeClass('voyager-double-down').addClass('voyager-double-up');
                } else {
                    $('#toggle_options .voyager-double-up').removeClass('voyager-double-up').addClass('voyager-double-down');
                }
            });

            @can('delete', Voyager::model('Setting'))
            $('.panel-actions .voyager-trash').on('click', function () {
                var display = $(this).data('display-name') + '/' + $(this).data('display-key');

                $('#delete_setting_title').text(display);

                $('#delete_form')[0].action = '{{ route('voyager.settings.delete', [ 'id' => '__id' ]) }}'.replace('__id', $(this).data('id'));
                $('#delete_modal').modal('show');
            });
            @endcan

            $('.toggleswitch').bootstrapToggle();

            $('[data-toggle="tab"]').on('click', function() {
                $(".setting_tab").val($(this).html());
            });

            $('.delete_value').on('click', function(e) {
                e.preventDefault();
                $(this).closest('form').attr('action', $(this).attr('href'));
                $(this).closest('form').submit();
            });

            // Initiliaze rich text editor
            tinymce.init(window.voyagerTinyMCE.getConfig());
        });
    </script>
    <script type="text/javascript">
    $(".group_select").not('.group_select_new').select2({
        tags: true,
        width: 'resolve'
    });
    $(".group_select_new").select2({
        tags: true,
        width: 'resolve',
        placeholder: '{{ __("voyager::generic.select_group") }}'
    });
    $(".group_select_new").val('').trigger('change');
    </script>
    <iframe id="form_target" name="form_target" class="d-none"></iframe>
    <form class="settings-upload" id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
        <input type="hidden" name="type_slug" id="type_slug" value="settings">
    </form>

    <script>
        try{
            var options_editor = ace.edit('options_editor');
            options_editor.getSession().setMode("ace/mode/json");

            var options_textarea = document.getElementById('options_textarea');
            options_editor.getSession().on('change', function() {
                console.log(options_editor.getValue());
                options_textarea.value = options_editor.getValue();
            });
        } catch (e) {
            // eslint-disable-next-line no-console
            console.warn(e);
        }

        var site_settings = {
            'emails.driver': "{{getSetting('emails.driver')}}",
            'storage.driver': "{{getSetting('storage.driver')}}",
        }

    </script>
@stop
