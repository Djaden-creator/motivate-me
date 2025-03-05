<?php

namespace App\Bundle;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SellMan
{

   // here to ucreate a user customer
    public function CreateCustomer(User $user,$payementortoken,EntityManagerInterface $entityManagerInterface)
    {
        $customer= \Stripe\Customer::create([
            'email' => $user->getEmail(),
            'source' => $payementortoken,
          ]);
        
           $user->setStripeidtoken($customer->id);
           $entityManagerInterface->persist($user);
           $entityManagerInterface->flush();
           return $customer;
    }

    // here to update a user customer  
    public function UpdateCustomer(User $user,$payementortoken)
    {
        $customer=\Stripe\Customer::retrieve($user->getStripeidtoken());
        $customer->source=$payementortoken;
        $customer->update($user->getStripeidtoken());
    }

    //here we create the invoice
    public function CreateInvoice($amount,$user,$description,$envdot)
    {      
         \Stripe\InvoiceItem::create(array(
          "amount"=>$amount,
          "currency"=>$envdot,
          //we are updating this source to the customer because we want to charger the customer now
          // "source"=>$token,
          "customer"=>$user,
          "description"=>$description,
          
        ));
    }

    //here to pay and create the invoice
    public function PayInvoice(User $user,$payimediately=true)
    {
          
        $invoice= \Stripe\Invoice::create([
            'customer' => $user->getStripeidtoken(),
          ]);
         if($payimediately)
         {
            $invoice->pay();
         } 
         return $invoice;
             
    }
    }
    
