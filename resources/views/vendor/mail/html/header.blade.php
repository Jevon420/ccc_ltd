@props(['url'])
<tr>
<td class="header" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); padding: 24px 32px; border-radius: 8px 8px 0 0;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<table cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td style="padding-right: 12px; vertical-align: middle;">
<div style="width: 42px; height: 42px; background-color: #2563eb; border-radius: 10px; text-align: center; line-height: 42px;">
<span style="color: #ffffff; font-weight: 800; font-size: 20px; font-family: -apple-system, sans-serif;">C</span>
</div>
</td>
<td style="vertical-align: middle;">
<p style="margin: 0; color: #ffffff; font-weight: 700; font-size: 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; letter-spacing: -0.02em;">{{ config('app.name') }}</p>
<p style="margin: 0; color: #93c5fd; font-size: 11px; font-family: -apple-system, sans-serif; margin-top: 2px; font-weight: 400;">Constructive Cleaning Company Ltd · Trinidad & Tobago</p>
</td>
</tr>
</table>
</a>
</td>
</tr>
