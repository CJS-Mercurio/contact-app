<?php

namespace App\Http\Controllers;

use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactController extends Controller
{

    public function __construct(protected CompanyRepository $company)
    {
        
    }

    public function index(CompanyRepository $company, Request $request)
    {

        // dd($request->sort_by);
        // $companies = [
        //     1 => ['name' => 'Company One', 'contacts' => 3],
        //     2 => ['name' => 'Company Two', 'contacts' => 5],
        // ];
        $companies = $company->pluck();

        // Display Soft deleted models
        // Pagination, Sorts, Filters, and Search
        $contacts = Contact::allowedTrash()
            ->allowedSorts('first_name')
            ->allowedFilters('company_id')
            ->allowedSearch('first_name', 'last_name', 'email')
            ->paginate(10);
        return view('contacts.index', compact('contacts', 'companies'));

        // // Creating Pagination Manually
        // $contactsCollection = Contact::latest()->get();
        // $perPage = 10;
        // $currentPage = request()->query('page', 1);
        // $items = $contactsCollection->slice(($currentPage * $perPage) - $perPage, $perPage);
        // $total = $contactsCollection->count();
        // $contacts = new LengthAwarePaginator($items, $total, $perPage, $currentPage, [
        //     'path' => request()->url(),
        //     'query' => request()->query()
        // ]);
        // $contacts = $this->getContacts();
    }

    public function create()
    {
        // dd(request()->method());
        $companies = $this->company->pluck();
        $contact = new Contact();
        return view('contacts.create', compact('companies', 'contact'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email',
            'phone' => 'nullable',
            'address' => 'nullable',
            'company_id' => 'required|exists:companies,id'
        ]);
        Contact::create($request->all());
        return redirect()->route('contacts.index')->with('message', 'Contact has been added successfully!');
    }

    public function show(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        // abort_if(empty($contact), 404);
        return view('contacts.show')->with('contact', $contact);
    }

    public function edit(Request $request, $id)
    {
        $companies = $this->company->pluck();
        $contact = Contact::findOrFail($id);
        // abort_if(empty($contact), 404);
        return view('contacts.edit', compact('contact', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email',
            'phone' => 'nullable',
            'address' => 'nullable',
            'company_id' => 'required|exists:companies,id'
        ]);
        $contact->update($request->all());
        return redirect()->route('contacts.index')->with('message', 'Contact has been added updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        $redirect = request()->query('redirect');
        return ($redirect ? redirect()->route($redirect) : back())
        ->with('message', 'Contact has been moved to trash.')
        ->with('undoRoute', $this->getUndoRoute('contacts.restore', $contact));
    }

    public function restore(Request $request, $id)
    {
        $contact = Contact::onlyTrashed()->findOrFail($id);
        $contact->restore();
        return back()
        ->with('message', 'Contact has been restored from trash.')
        ->with('undoRoute', $this->getUndoRoute('contacts.destroy', $contact));
    }

    public function getUndoRoute($name, $resource)
    {
        return request()->missing('undo') ? route($name, [$resource->id, 'undo' => true]) :null;
    }

    public function forceDelete(Request $request, $id)
    {
        $contact = Contact::onlyTrashed()->findOrFail($id);
        $contact->forceDelete();
        return back()
            ->with('message', 'Contact has been removed permanently.');
    }
}
