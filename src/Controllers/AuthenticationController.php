<?php

namespace SDU\MFA\Controllers;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Session\Session;
use Illuminate\Routing\Controller;
use SDU\MFA\Azure\Graph;
use SDU\MFA\User;

class AuthenticationController extends Controller
{
    public function callback()
    {
        $state = json_decode(base64_decode(request('state')));

        /** @var Session $session */
        $session = app(Session::class);
        
        if ($state->id != $session->getId())
            return response('Bad Request', 400);

        /** @var Repository $config */
        $config       = app(Repository::class);
        $clientId     = $config->get('sdu-mfa.client_id');
        $tenantId     = $config->get('sdu-mfa.tenant_id');
        $clientSecret = $config->get('sdu-mfa.client_secret');
        $redirectUri  = route('sdu.mfa.callback');

        $httpClient = new Client([
            'base_uri' => 'https://login.microsoftonline.com'
        ]);

        $response = $httpClient->post("https://login.microsoftonline.com/$tenantId/oauth2/token", [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => $clientId,
                'code'          => request('code'),
                'redirect_uri'  => $redirectUri,
                'client_secret' => $clientSecret,
                'resource'      => 'https://graph.microsoft.com'
            ]
        ]);

        if ($response->getStatusCode() != 200)
            return redirect($state->uri);

        $result      = json_decode($response->getBody()->getContents(), true);
        $accessToken = $result['access_token'];

        $azureUser = (new Graph($accessToken))->me();
        if ( ! $azureUser->hasAccess($this->adGroups()))
            return redirect(route('sdu.mfa.forbidden'));

        auth()->login($azureUser);

        return redirect($state->uri);
    }

    private function adGroups() : array
    {
        /** @var Repository $config */
        $config = app(Repository::class);
        $value  = $config->get('sdu-mfa.required_ad_groups');
        return is_string($value) ? explode('|', $value) : $value;
    }

    public function forbidden()
    {
        return view('SDU\MFA::forbidden');
    }
}