<?php

namespace Ferranfg\Base\Clients;

use SendinBlue\Client\ApiException;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\ContactsApi;
use GuzzleHttp\Client as GuzzleClient;
use SendinBlue\Client\Model\AddContactToList;
use SendinBlue\Client\Model\CreateContact;

class SendinBlue
{
    private static function getConfiguration()
    {
        return Configuration::getDefaultConfiguration()->setApiKey(
            config('services.sendinblue.key_identifier'),
            config('services.sendinblue.key')
        );
    }

    public static function contactsApi()
    {
        return new ContactsApi(new GuzzleClient, self::getConfiguration());
    }

    public static function addContactToList($list_id, $email)
    {
        try
        {
            self::contactsApi()->getContactInfo($email);
        }
        catch (ApiException $e)
        {
            if ($e->getCode() == 404) self::contactsApi()->createContact(new CreateContact([
                'email' => $email
            ]));
        }

        try
        {
            return self::contactsApi()->addContactToList($list_id, new AddContactToList([
                'emails' => [$email]
            ]));
        }
        catch (ApiException $e)
        {
            report($e);
        }
    }
}
