import random

def cards():
    deck = []
    suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades']
    values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A']

    for suit in suits:
        for value in values:
            deck.append((value, suit))  # Tuple of (value, suit)
    
    # Multiply by 4 to get 4 decks
    deck *= 4
    
    return deck  # returns 4 decks of 52 cards

def shuffler(deck):
    random.shuffle(deck)
    return deck


def deal(deck):
    return deck.pop()  # Removes and returns the last card from the deck


def hit(deck, hand):
    hand.append(deal(deck))  # Add the dealt card to the hand
    return hand

def stand():
    print("You chose to stand.")
    return

def calculate_hand_value(hand):
    value = 0
    aces = 0
    
    value_map = {
        '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9, '10': 10,
        'J': 10, 'Q': 10, 'K': 10, 'A': 11
    }
    
    for card in hand:
        card_value = card[0]
        value += value_map[card_value]
        if card_value == 'A':
            aces += 1
    
    # Adjust for aces
    while value > 21 and aces:
        value -= 10
        aces -= 1

    return value

def check_for_bust(hand):
    return calculate_hand_value(hand) > 21

def check_for_blackjack(hand):
    return calculate_hand_value(hand) == 21

def dealer_turn(deck, dealer_hand):
    while calculate_hand_value(dealer_hand) < 17:
        hit(deck, dealer_hand)
    
    return dealer_hand

def determine_winner(player_hand, dealer_hand):
    player_value = calculate_hand_value(player_hand)
    dealer_value = calculate_hand_value(dealer_hand)

    if dealer_value > 21:
        return "Dealer busts! You win!"
    elif player_value > dealer_value:
        return "You win!"
    elif player_value < dealer_value:
        return "Dealer wins!"
    else:
        return "It's a tie!"

def replay_game():
    choice = input("Do you want to play again? (y/n): ").lower()
    return choice == 'y'


def main():
    print("Welcome to Blackjack!")

    while True:
        # Create and shuffle the deck
        deck = cards()
        shuffler(deck)

        # Initial deal
        player_hand = [deal(deck), deal(deck)]
        dealer_hand = [deal(deck), deal(deck)]

        # Player's turn
        while True:
            print(f"Your hand: {player_hand}, value: {calculate_hand_value(player_hand)}")
            print(f"Dealer's visible hand: {dealer_hand[0]}")

            if check_for_blackjack(player_hand):
                print("Blackjack! You win!")
                break

            action = input("Do you want to 'hit' or 'stand'? ").lower()

            if action == 'hit':
                hit(deck, player_hand)
                print(f"Your new hand: {player_hand}, value: {calculate_hand_value(player_hand)}")

                if check_for_bust(player_hand):
                    print("You bust! Dealer wins!")
                    break
            elif action == 'stand':
                stand()
                break
            else:
                print("Invalid input. Please choose 'hit' or 'stand'.")

        if not check_for_bust(player_hand):
            # Dealer's turn
            dealer_hand = dealer_turn(deck, dealer_hand)
            print(f"Dealer's hand: {dealer_hand}, value: {calculate_hand_value(dealer_hand)}")

            # Determine the winner
            result = determine_winner(player_hand, dealer_hand)
            print(result)

        # Replay or exit
        if not replay_game():
            print("Thanks for playing!")
            break

main()


main()
