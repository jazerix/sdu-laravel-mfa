<?php

namespace SDU\MFA\Controllers;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Session\Session;
use Illuminate\Routing\Controller;
use SDU\MFA\Azure\Graph;
use SDU\MFA\Azure\User;

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

        auth()->login($this->getUser($azureUser));

        return redirect($state->uri);
    }

    private function getUser(User $user)
    {
        if (config('sdu-mfa.persist_users') == null)
            return $user;


        return $this->updateUser($user);
    }

    private function updateUser(User $user)
    {
        $userClass = config('sdu-mfa.persist_users');
        $dbUser    = null;
        $userClass::unguarded(function () use (&$dbUser, $user, $userClass)
        {
            $dbUser = $userClass::updateOrCreate([
                'guid' => $user->getId(),
            ], [
                'name'       => $user->getDisplayName(),
                'given_name' => $user->getGivenName(),
                'sur_name'   => $user->getSurname(),
                'email'      => $user->getMail(),
                'title'      => $user->getJobTitle(),
                'ad_groups'  => collect($user->getGroupCollection()->toArray())->pluck('id')
            ]);
        });

        return $dbUser;
    }

    private function adGroups() : array
    {
        /** @var Repository $config */
        $config = app(Repository::class);
        $value  = $config->get('sdu-mfa.required_ad_groups');
        return is_string($value) ? explode('|', $value) : (is_null($value) ? [] : $value);
    }

    public function forbidden()
    {
        return view('SDU\MFA::forbidden');
    }
}
