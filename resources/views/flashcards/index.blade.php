<!DOCTYPE html>
<html>
<head>
    <title>Thai SRS App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
            position: relative;
        }
        .container {
            width: 300px;
            text-align: center;
        }
        .flashcard {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 10px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            font-size: 32px;
        }
        .hidden {
            display: none;
        }
        .actions {
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>

    @auth
        <div class="logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
        <div class="container">
            <h1>Review Words</h1>
            <p id="progress">0 / 0</p>
            @foreach ($flashcards as $flashcard)
                <div class="flashcard hidden" data-id="{{ $flashcard->id }}">
                    <p class="word">{{ $flashcard->word }}</p>
                    <p class="meaning hidden">{{ $flashcard->meaning }}</p>
                    <p class="pronunciation hidden">{{ $flashcard->pronunciation }}</p>
                    <div class="actions hidden">
                        <form action="/review" method="POST" class="know-form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $flashcard->id }}">
                            <input type="hidden" name="language" value="{{ $language }}">
                            <input type="hidden" name="known" value="1">
                            <button type="submit">I know</button>
                        </form>
                        <form action="/review" method="POST" class="dont-know-form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $flashcard->id }}">
                            <input type="hidden" name="language" value="{{ $language }}">
                            <input type="hidden" name="known" value="0">
                            <button type="submit">I don't know</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>Please <a href="{{ route('login') }}">login</a> or <a href="{{ route('register') }}">register</a> to start using the SRS app.</p>
    @endauth

    <script>
        let currentCardIndex = 0;
        const cards = document.querySelectorAll('.flashcard');
        const progress = document.getElementById('progress');

        function updateProgress() {
            progress.textContent = `${currentCardIndex + 1} / ${cards.length}`;
        }

        function showCard(index) {
            cards.forEach((card, i) => {
                card.classList.add('hidden');
                if (i === index) {
                    card.classList.remove('hidden');
                }
            });
            updateProgress();
        }

        function flipCard(card) {
            const word = card.querySelector('.word');
            const meaning = card.querySelector('.meaning');
            const pronunciation = card.querySelector('.pronunciation');
            const actions = card.querySelector('.actions');
            word.classList.toggle('hidden');
            meaning.classList.toggle('hidden');
            pronunciation.classList.toggle('hidden');
            actions.classList.toggle('hidden');
        }

        function handleFormSubmit(form) {
            const formData = new FormData(form);
            fetch(form.action, {
                method: form.method,
                body: formData
            }).then(response => {
                if (response.ok) {
                    currentCardIndex++;
                    showCard(currentCardIndex);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (cards.length > 0) {
                showCard(currentCardIndex);
            }
            cards.forEach(card => {
                card.addEventListener('click', () => flipCard(card));
            });

            document.addEventListener('keydown', (event) => {
                const currentCard = cards[currentCardIndex];
                const isFlipped = !currentCard.querySelector('.meaning').classList.contains('hidden');
                
                switch (event.key) {
                    case ' ':
                        flipCard(currentCard);
                        break;
                    case 'a':
                    case 's':
                    case 'd':
                        if (isFlipped) {
                            handleFormSubmit(currentCard.querySelector('.dont-know-form'));
                        }
                        break;
                    case 'Enter':
                    case ',':
                    case ';':
                    case '\\':
                    case ']':
                    case '[':
                    case '\'':
                        if (isFlipped) {
                            handleFormSubmit(currentCard.querySelector('.know-form'));
                        }
                        break;
                }
            });
        });
    </script>
</body>
</html>
