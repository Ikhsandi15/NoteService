<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    public function noteLists(Request $req)
    {
        $category_id = $req->query('category_id');
        $sort_by = $req->query('sort_by', 'title');

        $datas = Note::with([
            'user' => function ($query) {
                $query->select('id', 'name', 'email');
            },
            'category' => function ($query) {
                $query->select('id', 'category');
            }
        ])
            ->where('user_id', Auth::id())
            ->when($category_id, function ($query, $category_id) {
                return $query->where('category_id', $category_id);
            })->orderBy($sort_by)->get();

        $datas->makeHidden('user_id', 'category_id');

        return Helper::APIResponse('Successfully retrieved the category list', 200, null, $datas);
    }

    public function detail($note_id)
    {
        $note = Note::with([
            'category' => function ($query) {
                $query->select('id', 'category');
            },
            'user' => function ($query) {
                $query->select('id', 'name', 'email');
            }
        ])->where('user_id', Auth::id())->find($note_id);

        if (!$note) {
            return Helper::APIResponse('data not found', 404, 'not found', null);
        }
        $note->makeHidden(['category_id', 'user_id']);

        return Helper::APIResponse('success', 200, null, $note);
    }

    public function create(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse('error validation', 422, 'error validation', null);
        }

        $imageName = null;
        if ($req->hasFile('image')) {
            $image = $req->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images/' . $imageName);
        }

        $note = Note::create([
            'title' => $req->title,
            'content' => $req->content,
            'user_id' => Auth::id(),
            'category_id' => $req->category_id,
            'image' => $imageName
        ]);

        return Helper::APIResponse('success create note', 200, null, null);
    }

    public function update(Request $req, $note_id)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse('error validation', 422, 'error validation', null);
        }

        $note = Note::where('user_id', Auth::id())->find($note_id);

        if (!$note) {
            return Helper::APIResponse('data not found', 404, 'not found', null);
        }

        $imageName = null;
        if ($req->hasFile('image')) {
            if ($note->image != null) {
                Storage::disk('public')->delete('images/' . $note->image);
            }
            $image = $req->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images/' . $imageName);
        }

        $note->update([
            'title' => $req->title,
            'content' => $req->content,
            'user_id' => Auth::id(),
            'category_id' => $req->category_id,
            'image' => $imageName ? $imageName : $note->image
        ]);

        return Helper::APIResponse('success update note', 200, null, null);
    }

    public function delete($note_id)
    {
        $note = Note::find($note_id);

        if (!$note) {
            return Helper::APIResponse('data not found', 404, 'not found', null);
        }

        if ($note->image != null) {
            Storage::disk('public')->delete('images/' . $note->image);
        }

        $note->delete();

        return Helper::APIResponse('success delete note', 200, null, null);
    }

    public function toggleFavorite(Request $req, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return Helper::APIResponse('data not found', 404, 'not found', null);
        }

        $note->favorite = !$note->favorite;
        $note->save();

        $data = $note->only(['id', 'title']);

        return Helper::APIResponse('success', 200, null, $data);
    }

    public function favoriteLists()
    {
        $datas = Note::where('user_id', Auth::id())->where('favorite', true)->get();

        return Helper::APIResponse('success', 200, null, $datas);
    }

    public function search(Request $req)
    {
        $req->validate([
            'query' => 'required|string|min:1'
        ]);

        $query = $req->input('query');

        $notes = Note::with([
            'category' => function ($query) {
                $query->select('id', 'category');
            },
            'user' => function ($query) {
                $query->select('id', 'name', 'email');
            }
        ])
            ->where('user_id', Auth::id())
            ->where('title', 'LIKE', '%' . $query . '%')
            ->orWhere('content', 'LIKE', '%' . $query . '%')
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('category', 'LIKE', '%' . $query . '%');
            })
            ->get();

        $notes->makeHidden('category_id', 'user_id');

        return Helper::APIResponse('success', 200, null, $notes);
    }
}
