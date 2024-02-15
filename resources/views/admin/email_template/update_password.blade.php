@include('admin.email_template.header')
<tr>
    <td>
        <table cellspacing="0" cellpadding="0" style="background: #ebebeb; min-width: 100%; text-align: left;">
            <tr>
                <td style="padding: 25px;">
                    <table cellspacing="0" cellpadding="0" style="width: 640px; margin: 0px auto; background: #fff;">
                        <tr>
                            <td style=" vertical-align: top; text-align: center; padding: 50px 20px 30px 20px;">
                                <h4
                                    style="color: #2e2e2e; font-weight: 700; font-size: 20px; line-height: 1; margin: 0 0 25px;">
                                    Hello
                                    <span style="color: #78be20;">{{ $mailcontent['name'] }}</span>,
                                </h4>
                                <p
                                    style="font-weight: 500; color: #455460; font-size: 16px; margin: 0 0 30px; line-height: 1.5;">
                                    {{ $mailcontent['message'] }}
                                </p>
                                <p style="margin: 0 0 30px;">
                                    <span style="display: block; clear: both;"></span>
                                    <a style="background: #78be20; display: inline-block; color: #ffffff; padding: 14px 30px; font-weight: 700; text-decoration: none; border-radius: 6px; font-size: 15px;"
                                        href="{{ $mailcontent['login_url'] }}">PLEASE CLICK HERE TO LOGIN</a>
                                    <span style="display: block; clear: both;"></span>
                                </p>
                                <p
                                    style="font-weight: 500; color: #455460; font-size: 16px; margin: 0 0 30px; line-height: 1.5;">
                                    If you did not intiate this request, Please contact us immediately.</p>
                            </td>
                        </tr>

                        @include('admin.email_template.footer')