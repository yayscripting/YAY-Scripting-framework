<h1>431 Request Header Fields Too Large</h1>

<p>
The server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large.
</p>
{if $error != ''}
<p><em>{$error}</em></p>
{/if}