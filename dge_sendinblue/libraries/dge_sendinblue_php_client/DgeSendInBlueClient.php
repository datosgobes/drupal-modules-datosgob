<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_sendinblue (datos.gob.es)".
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 2 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class DgeSendInBlueClient {
    private int $maxNumberRequestTries;
    private string $apiKey;
    private bool $debug = false;
    private SendinBlue\Client\Configuration $config;

    private SendinBlue\Client\Api\FoldersApi $foldersApiInstance;
    private SendinBlue\Client\Api\ListsApi $listsApiInstance;
    private SendinBlue\Client\Api\ContactsApi $contactsApiInstance;
    private SendinBlue\Client\Api\AccountApi $accountApiInstance;
    private SendinBlue\Client\Api\EmailCampaignsApi $emailCampaingsApiInstance;
    private SendinBlue\Client\Api\SendersApi $sendersApiInstance;

    public $list;
    public $account;
    public int $listId;

    public function __construct(string $api_key, ?int $list_id = null, bool $debug = false, int $max_number_request_tries = 3) {
        $this->maxNumberRequestTries = $max_number_request_tries;
        $this->apiKey = $api_key;
        $this->config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->apiKey);
        $this->foldersApiInstance = new SendinBlue\Client\Api\FoldersApi(new GuzzleHttp\Client(), $this->config);
        $this->listsApiInstance = new SendinBlue\Client\Api\ListsApi(new GuzzleHttp\Client(), $this->config);
        $this->contactsApiInstance = new SendinBlue\Client\Api\ContactsApi(new GuzzleHttp\Client(), $this->config);
        $this->accountApiInstance = new SendinBlue\Client\Api\AccountApi(new GuzzleHttp\Client(), $this->config);
        $this->emailCampaingsApiInstance = new SendinBlue\Client\Api\EmailCampaignsApi(new GuzzleHttp\Client(), $this->config);
        $this->sendersApiInstance = new SendinBlue\Client\Api\SendersApi(new GuzzleHttp\Client(), $this->config);

        if ($list_id) {
            try {
                $this->listId = (int) $list_id;
                $this->list = $this->getList();
                $this->account = $this->getAccount();
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw new \Exception('Missing parameters');
        }
    }


    private function requestWrapper(callable $function, int $tries = 0, ...$args) {
        try {
            return call_user_func_array($function, $args);
        } catch (\Exception $e) {
            if ($tries > $this->maxNumberRequestTries - 1) {
                throw $e;
            } else {
                return $this->requestWrapper($function, $tries + 1, ...$args);
            }
        }
    }

    private function getAccount() {
        try {
            return $this->requestWrapper([$this->accountApiInstance, 'getAccount']);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getList() {
        try {
            return $this->requestWrapper([$this->contactsApiInstance, 'getList'], 0, $this->listId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function syncContactsToList($list) {
        if ('string' === gettype($list)) {
            $list = [$list];
        }
        try {
            $contacts_form_list           = $this->getContactsFromList();
            $no_existing_account_in_source = array_diff($contacts_form_list, $list);
            $this->removeContactFromList($no_existing_account_in_source);

            $this->addSubscriptorsToList($list);
        } catch (Exception $e) {
            throw  $e;
        }
    }

    public function getContactsFromAccount($offset = 0) {
        $limit = 500;
        try {
            $result = $this->requestWrapper([$this->contactsApiInstance, 'getContacts'], 0, $limit, $offset);
            if ($result->getContacts()) {
                $result = $result->getContacts();
                $result = array_map(function ($e) {
                    return $e['email'];
                }, $result);

                return array_merge($result, $this->getContactsFromAccount($offset + $limit));
            } else {
                return [];
            }
        } catch (Exception $e) {
            throw  $e;
        }
    }

    public function getContactsFromList($offset = 0) {
        $limit = 500;
        try {
            $result = $this->requestWrapper([$this->contactsApiInstance, 'getContactsFromList'], 0, $this->listId, null, $limit, $offset);
            if ($result->getContacts()) {
                $result = $result->getContacts();
                $result = array_map(function ($e) {
                    return $e['email'];
                }, $result);

                return array_merge($result, $this->getContactsFromList($offset + $limit));
            } else {
                return [];
            }
        } catch (Exception $e) {
            throw  $e;
        }
    }

    public function createEmailCampaign($sender, $name, $subject, $html_content, $footer = null) {
        $email_campaigns = new SendinBlue\Client\Model\CreateEmailCampaign();
        $email_campaign_sender = new SendinBlue\Client\Model\CreateEmailCampaignSender();
        $email_campaign_sender->setEmail($sender);
        $email_campaigns->setSender($email_campaign_sender);
        $email_campaigns->setName($name);
        $email_campaigns->setSubject($subject);
        $email_campaigns->setHtmlContent($html_content);
        $email_campaigns->setFooter($footer);
        $recipients = new SendinBlue\Client\Model\CreateEmailCampaignRecipients();
        $recipients->setListIds([$this->listId]);

        $email_campaigns->setRecipients($recipients);

        try {
            $campaing = $this->requestWrapper([$this->emailCampaingsApiInstance, 'createEmailCampaign'], 0, $email_campaigns);
            return $campaing;
        } catch (Exception  $e) {
            throw  $e;
        }
    }

    public function sendEmailCampaignNow($campaign_id) {
        try {
            $campaing = $this->requestWrapper([$this->emailCampaingsApiInstance, 'sendEmailCampaignNow'], 0, $campaign_id);
        } catch (Exception  $e) {
            throw  $e;
        }
    }

    public function createSender($sender_email) {
        $sender = new SendinBlue\Client\Model\CreateSender();
        $sender->setName($sender_email);
        $sender->setEmail($sender_email);
        try {
            $result = $this->requestWrapper([$this->sendersApiInstance, 'createSender'], 0, $sender);
        } catch (Exception  $e) {
            throw  $e;
        }
    }

    private function createContact($email) {
        $create_contact = new \SendinBlue\Client\Model\CreateContact();
        $create_contact->setEmail($email);
        $create_contact->setListIds([$this->listId]);
        try {
            $result = $this->requestWrapper([$this->contactsApiInstance, 'createContact'], 0, $create_contact);
        } catch (Exception  $e) {
            throw  $e;
        }
    }

    private function addContactToList($contacts) {
        if ('string' === gettype($contacts)) {
            $contacts = [$contacts];
        }
        $chunks = array_chunk($contacts, 150);
        foreach ($chunks as $chunk) {
            $contact_emails = new \SendinBlue\Client\Model\AddContactToList();

            $contact_emails->setEmails($chunk);
            try {
                $this->requestWrapper([$this->listsApiInstance, 'addContactToList'], 0, $this->listId, $contact_emails);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    public function removeContactFromList($contacts) {
        if ('string' === gettype($contacts)) {
            $contacts = [$contacts];
        }
        $chunks = array_chunk($contacts, 150);
        foreach ($chunks as $chunk) {
            $contact_emails = new \SendinBlue\Client\Model\RemoveContactFromList();

            $contact_emails->setEmails($chunk);
            try {
                $this->requestWrapper([$this->listsApiInstance, 'removeContactFromList'], 0, $this->listId, $contact_emails);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    public function addSubscriptorsToList($emails) {
        if ('string' === gettype($emails)) {
            $emails = [$emails];
        }

        $emails = array_map(function ($e) {
            return strtolower($e);
        }, $emails);
        $contacts_form_account = $this->getContactsFromAccount();
        $no_existing_account  = array_diff($emails, $contacts_form_account);

        foreach ($no_existing_account as $email) {
            try {
                $this->createContact($email);
            } catch (Exception $e) {
                if ('duplicate_parameter' === $this->getValueResponse($e->getMessage())) {
                    continue;
                } else {

                    throw $e;
                }
            }
        }
        $contacts_form_list         = $this->getContactsFromList();
        $no_existing_account_in_list = array_diff($emails, $contacts_form_list);
        try {
            $this->addContactToList($no_existing_account_in_list);
        } catch (Exception $e) {
            if (!'invalid_parameter' === $this->getValueResponse($e->getMessage())) {
                throw $e;
            }
        }
    }

    private function getResponse($string) {
        return json_decode(explode('response:', $string)[1]);
    }

    private function getValueResponse(string $string, int $key = 1) {
        return explode(':', $string)[$key];
    }
}
