<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flashcard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;


class FlashcardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($language)
    {

        $flashcard = new Flashcard();

        $flashcard->setTable($language);  


        $flashcards = $flashcard->where('user_id', Auth::id())
                               ->where('nextReviewDate', '<=', Carbon::now())
                               ->where('mastered', false)
                               ->get();
        return view('flashcards.index', compact('flashcards', 'language'));
    }

    public function review(Request $request)
    {

        $flashcard = new Flashcard();
        $flashcard->setTable($request->language);  // Dynamically set the table from the request
        
        $flashcard = $flashcard->where('id', $request->id)->first();

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

        return redirect("/flash/$request->language");

    }


    public function seedFromJson()
    {
        $directory = database_path('seeders/seeds');
        $files = File::allFiles($directory);

        foreach($files as $file) {

            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {

                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $filePath = $file->getRealPath();
                

                $json = File::get($filePath);
                $flashcards = json_decode($json, true);

                $flashcard = new Flashcard();

                if (!Schema::hasTable($fileName)) {
                    throw new \Exception('Table does not exist: ' . $fileName);
                }
                $flashcard->setTable($fileName);

                foreach ($flashcards as $flashcardData) {


                    //suprisingly necessary, chatgpt on word creation often mispells meaning...for a randmon json line
                    $word = $flashcardData['word'] ?? null;
                    $meaning = $flashcardData['meaning'] ?? null;
                    $pronunciation = $flashcardData['pronunciation'] ?? null;
                    if (!$word || !$meaning) {
                        // Skip if essential data is missing
                        continue;
                    }

                    $exists = $flashcard->where('user_id', Auth::id())
                                        ->where('word', $flashcardData['word'])
                                        ->where('meaning', $flashcardData['meaning'])
                                        ->exists();
        
                    if (!$exists) {
                        // Create a new instance of the Flashcard model for saving
                        $newFlashcard = $flashcard->newInstance();
                        $newFlashcard->user_id = Auth::id();
                        $newFlashcard->word = $flashcardData['word'];
                        $newFlashcard->meaning = $flashcardData['meaning'];
                        $newFlashcard->pronunciation = $flashcardData['pronunciation'];
                        $newFlashcard->nextReviewDate = Carbon::now();
                        $newFlashcard->save();
                    }
                }
            }
        }
        return redirect('/');
        //it throws an error but still seeds, soo dunno
    }


    

}
