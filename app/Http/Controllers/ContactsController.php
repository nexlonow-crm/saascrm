<?php 
use App\Domain\Contacts\Models\Contact;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function index()
    {
        $contacts = Contact::where('account_id', auth()->user()->account_id)
            ->latest()
            ->paginate(15);

        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['nullable', 'email'],
            'phone'      => ['nullable', 'string', 'max:50'],
        ]);

        $data['account_id'] = auth()->user()->account_id;
        $data['tenant_id']  = auth()->user()->tenant_id; // adjust to your design

        Contact::create($data);

        return redirect()->route('contacts.index')->with('status', 'Contact created.');
    }

    // add show/edit/update/destroy later
}
