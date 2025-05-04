<p>Dear {{ $seller->name }},</p>

<p>You have received a new service request through the TechConnect application.</p>

<p>Client Details:</p>
<ul>
    <li>Name: {{ $client->name }}</li>
    <li>Contact Number: {{ $client->phone }}</li>
    <li>Email Address: {{ $client->email }}</li>
</ul>

<p>Service Details:</p>
<ul>
    <li>Service Type: {{ $service->title }}</li>
    <li>Service Location: {{ $client->ci }}</li>
</ul>

<p>The client has requested a quote for the service. Please review the request and submit your quote through the TechConnect application.</p>

<p>You can view the complete service request details within the application.</p>

<p>Thank you for using TechConnect.</p>

<p>Sincerely,<br>
The TechConnect Team</p>
