<?php
enum Suit
{
    case Hearts;

    case Diamonds;

    case Clubs;

    case Spades;
}

enum Blazer : string implements Card, Deck
{
    case Hearts = 'H';

    case Diamonds = 'D';

    case Clubs = 'C';

    case Spades = 'S';
}
