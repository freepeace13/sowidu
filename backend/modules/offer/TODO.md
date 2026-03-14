# Offer Module

## Features

-   Company can create offer.
-   After offer is accepted an "Order" will automatically be created based on the offer.
-   Order can have many "offers".

## TODO

-   [x] Tax implementation
-   [x] Offer must have the company default tax
-   [x] Private user side interaction on offers.
-   [ ] Add "Offers" link to "Orders" page
-   [ ] Create a migration to remove `offer_id` on `orders` table

## Related tasks

-   On every user registration, bind new user to addressbook
