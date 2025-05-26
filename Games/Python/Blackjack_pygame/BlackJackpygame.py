import pygame
import random
import os

# Card class
class Card:
    def __init__(self, suit, rank, value):
        self.suit = suit
        self.rank = rank
        self.value = value
        file_path = os.path.join('C:\Bunker\FTP\Games\Blackjack_pygame\cards', f'{rank}_of_{suit}.png')
        original_image = pygame.image.load(file_path)
        self.image = pygame.transform.scale(original_image, (100, 150))  # Resize to 100x150 pixels

# Deck class
class Deck:
    def __init__(self):
        self.cards = []
        suits = ['hearts', 'diamonds', 'clubs', 'spades']
        ranks = {'2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9, '10': 10, 'jack': 10, 'queen': 10, 'king': 10, 'ace': 11}
        for suit in suits:
            for rank, value in ranks.items():
                self.cards.append(Card(suit, rank, value))
        random.shuffle(self.cards)

    def deal(self):
        return self.cards.pop()

# Player and Dealer classes
class Player:
    def __init__(self):
        self.hand = []
        self.score = 0

    def add_card(self, card):
        self.hand.append(card)
        self.score += card.value

    def reset(self):
        self.hand = []
        self.score = 0

class Dealer(Player):
    def __init__(self):
        super().__init__()
        self.hidden = True

    def reveal(self):
        self.hidden = False

# Initialize pygame
pygame.init()
screen = pygame.display.set_mode((1000, 800))
clock = pygame.time.Clock()
font = pygame.font.Font(None, 36)
running = True
game_over = False
player_turn = True
dt = 0

# Game objects
deck = Deck()
player = Player()
dealer = Dealer()

def deal_initial_cards():
    player.reset()
    dealer.reset()
    dealer.hidden = True
    player.add_card(deck.deal())
    player.add_card(deck.deal())
    dealer.add_card(deck.deal())
    dealer.add_card(deck.deal())

deal_initial_cards()

# Main loop
while running:
    # poll for events
    for event in pygame.event.get():
        if event.type == pygame.QUIT:
            running = False

        if not game_over and player_turn:
            if event.type == pygame.KEYDOWN:
                if event.key == pygame.K_h:  # Player hits
                    player.add_card(deck.deal())
                    player_turn = False
                    if player.score > 21:
                        game_over = True
                        result_text = "Player busts! Dealer wins."
                    player_turn = True

                if event.key == pygame.K_s:  # Player stands
                    player_turn = False
                    dealer.reveal()
                    while dealer.score < 17:  # Dealer hits until 17 or more
                        dealer.add_card(deck.deal())
                    if dealer.score > 21:
                        result_text = "Dealer busts! Player wins."
                    elif player.score > dealer.score:
                        result_text = "Player wins!"
                    else:
                        result_text = "Dealer wins!"
                    game_over = True

        if game_over:
            if event.type == pygame.KEYDOWN and event.key == pygame.K_r:
                game_over = False
                player_turn = True
                deck = Deck()  # Reinitialize the deck to reset it
                deal_initial_cards()

    # Render everything
    screen.fill("green")

    # Calculate positions to center cards
    player_card_start_x = 500 - (len(player.hand) * 100 + (len(player.hand) - 1) * 20) // 2  # Adjust spacing between cards
    dealer_card_start_x = 500 - (len(dealer.hand) * 100 + (len(dealer.hand) - 1) * 20) // 2

    # Render player's cards
    for idx, card in enumerate(player.hand):
        screen.blit(card.image, (player_card_start_x + idx * 120, 550))  # Adjust y-coordinate as needed

    # Render dealer's cards
    for idx, card in enumerate(dealer.hand):
        if dealer.hidden and idx == 0:
            pygame.draw.rect(screen, (0, 0, 0), pygame.Rect(dealer_card_start_x + idx * 120, 150, 100, 150))  # Adjust hidden card size
        else:
            screen.blit(card.image, (dealer_card_start_x + idx * 120, 150))

            
    if game_over:
        text = font.render(result_text, True, (255, 255, 255))
        screen.blit(text, (400 - text.get_width() // 2, 400 - text.get_height() // 2))

        text = font.render("Press R to Restart", True, (255, 255, 255))
        screen.blit(text, (400 - text.get_width() // 2, 450 - text.get_height() // 2))

    pygame.display.flip()
    dt = clock.tick(60) / 1000

pygame.quit()
