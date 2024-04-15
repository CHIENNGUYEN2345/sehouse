<div style="max-width:700px;margin:10px auto 20px">
    <div>
        <div style="display:inline-block;margin:0px auto">

            <table style="margin:0 auto;border-collapse:collapse;background-color:#fff;border-radius:10px">

                <tbody>
                <tr>
                    <td style="padding:10px;border-bottom:1px solid #e5e5e5">
                        <table style="width:100%;border-collapse:collapse">
                            <tbody>
                            <tr>
                                <td style="width:140px">
                                    <img src="{{\App\Http\Helpers\CommonHelper::getUrlImageThumb($settings['logo'])}}" class="{{$settings['name']}}">

                                </td>
                                <td style="padding-left:10px;font:16px/1.6 Arial;color:#000;font-weight:bold;text-align:right">{{trans('admin.activate_account')}} {{@$settings['name']}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>


                <tr>
                    <td style="padding:0 20px">
                        <div style="margin:20px 0 0">

                            <table style="width:660px;border-collapse:collapse;word-break:break-all;box-sizing:border-box">

                                <tbody>
                                <tr>

                                    <td style="padding:0 0 10px;font-family:Arial;font-size:15px;line-height:1.6;color:#000;font-weight:bold">

                                        {{trans('admin.welcome')}} {{@$data['user']->name}}<br>

                                        {{trans('admin.thanks')}} {{@$settings['name']}}.

                                    </td>

                                </tr>

                                <tr>

                                    <td style="font:15px/1.8 Arial;color:#000">

                                        {{trans('admin.you_register')}} {{@$settings['name']}} {{trans('admin.info')}}:<br>

                                        {{trans('admin.email')}}: {{@$data['user']->email}}<br>

                                        {{trans('admin.password')}}: {{@$data['user']->password}}

                                    </td>

                                </tr>


                                <tr>

                                    <td style="font:15px/1.8 Arial;color:#000">

                                        {{trans('admin.click')}} {{@$settings['name']}} {{trans('admin.you')}}<br>

                                        <a href="{{@$data['user']->link}}">{{@$data['user']->link}}</a>

                                    </td>

                                </tr>

                                <tr>

                                    <td style="padding:10px 0px;font:15px/1.8 Arial;font-weight:bold;color:#d23737">
                                        <b>{{trans('admin.support')}}:</b><br>
                                        {{trans('admin.phone')}}: {{ @$settings['hotline'] }}<br>
                                        {{trans('admin.email')}}: {{ @$settings['email'] }}<br>
                                        {{trans('admin.fanpage')}} : {{ @$settings['fanpage'] }}

                                    </td>

                                </tr>

                                </tbody></table>

                        </div>



                    </td>
                </tr>


                <tr>
                    <td style="padding:10px 20px;background:#f8f8f8;border-top:1px solid #e5e5e5;border-radius:0 0 10px 10px" id="m_-3744433325623262737m_5338770243025797393m_2723588182153450401m_5561831045043488677vi-VN">
                        <table style="width:100%;border-collapse:collapse">
                            <tbody>
                            <tr>
                                <td style="width:160px;text-align:center">
                                    <img src="{{\App\Http\Helpers\CommonHelper::getUrlImageThumb($settings['logo'])}}" class="{{$settings['name']}}">
                                </td>
                                <td style="padding-left:30px;font:15px/1.6 Arial;color:#666">
                                    <span style="font-weight:bold">{{@$settings['name']}}!</span><br>
                                    Copyright ©{{@$settings['name']}}. All Rights Reserved.
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                </tbody>
            </table>

        </div>
    </div>
</div>
