<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flashcard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class FlashcardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $flashcards = Flashcard::where('user_id', Auth::id())
                               ->where('nextReviewDate', '<=', Carbon::now())
                               ->where('mastered', false)
                               ->get();
        return view('flashcards.index', compact('flashcards'));
    }

    public function review(Request $request)
    {
        $flashcard = Flashcard::find($request->id);
        $known = $request->known;

        if ($known) {
            if ($flashcard->repetitions == 0) {
                $flashcard->interval = 1;
            } elseif ($flashcard->repetitions == 1) {
                $flashcard->interval = 6;
            } else {
                $flashcard->interval = round($flashcard->interval * $flashcard->easeFactor);
            }
            $flashcard->repetitions += 1;

            if ($flashcard->repetitions >= 5) {
                $flashcard->mastered = true;
            }
        } else {
            $flashcard->repetitions = 0;
            $flashcard->interval = 1;
        }

        $flashcard->nextReviewDate = Carbon::now()->addDays($flashcard->interval);
        $flashcard->save();

        return redirect('/');
    }

    public function seedFromJson()
    {
        $path = database_path('seeders/flashcards.json');
        if (!File::exists($path)) {
            throw new \Exception('File does not exist at path: ' . $path);
        }

        $json = File::get($path);
        $flashcards = json_decode($json, true);

        foreach ($flashcards as $flashcardData) {
            $exists = Flashcard::where('user_id', Auth::id())
                                ->where('word', $flashcardData['word'])
                                ->where('meaning', $flashcardData['meaning'])
                                ->exists();

            if (!$exists) {
                $flashcard = new Flashcard();
                $flashcard->user_id = Auth::id();
                $flashcard->word = $flashcardData['word'];
                $flashcard->meaning = $flashcardData['meaning'];
                $flashcard->pronunciation = $flashcardData['pronunciation'];
                $flashcard->nextReviewDate = Carbon::now();
                $flashcard->save();
            }
        }

        return redirect('/');
    }
}
