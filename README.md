# shopcollab

A simple collaborative shopping list tool, to allow multiple users to vote and add items to a shopping list for your office.
Site visitors can vote for items they would like, undo their own votes, and add items both new and old to the list, arranged by categories of your choosing.
The list owner can use the Admin Tools to mark the shopping as done, undo the last shopping trip, manage the historical item master list, and add historical items in bulk to the shopping list.

# Dependencies:
Apache 2.4
PHP 7.2
MYSQL 8.0.11

Probably it works with lower versions but this is what I wrote it with.

# Setup:

1. Set your admin password following the instructions in config.php
2. Run the contents of db.sql in your db, and create a user with privileges on the shopping_list database.
3. Update the database credentials in config.php to match your new user and db.
4. Navigate to /new-item.php in a web browser, log in, and create at least one item per category you would like (food drink supplies etc.)

# Customization:

Depending on how many people you have, or your security requirements for this shopping list you may want to tweak some things.
The CUTOFF variables defined in config.php determine how items on the shopping list will visually display their priority.
The defaults were intended for an office of about 15 people with shopping runs once every two weeks.

# Notes:

Admin authentications are session specific and expire after one day of inactivity.
Since this is not currently built for user specificity, the system is vulnerable to vote inflation by someone who knows how to change their cookies.
Sessions live for a year, but if a user closes their browser they will not be able to unvote for their old items.
