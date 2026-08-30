<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Show the login form */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    /** Handle login form submission */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user()->role);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    /** Show the vendor registration form */
    public function showVendorRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        $events = Event::where('status', 'active')->orderBy('name')->get();
        if ($events->isEmpty()) {
            $this->getOrCreateActiveEventId();
            $events = Event::where('status', 'active')->orderBy('name')->get();
        }

        return view('auth.register-vendor', compact('events'));
    }

    /** Handle vendor registration submission */
    public function registerVendor(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'         => ['nullable', 'string', 'max:20', 'unique:users'],
            'password'      => ['required', 'string', 'min:6', 'confirmed'],
            'event_id'      => ['nullable', 'exists:events,id'],
            'logo'          => ['nullable', 'image', 'max:2048'],
        ]);

        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $file     = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/uploads'), $filename);
            $logoUrl = '/images/uploads/' . $filename;
        }

        $eventId = $this->getOrCreateActiveEventId($validated['event_id'] ?? null);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => 'vendor',
            'password' => Hash::make($validated['password']),
        ]);

        Vendor::create([
            'user_id'       => $user->id,
            'business_name' => $validated['business_name'],
            'event_id'      => $eventId,
            'status'        => 'active',
            'logo_url'      => $logoUrl,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/vendor')->with('success', 'Vendor account created successfully!');
    }

    /** Logout */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** Redirect user to their role-specific view */
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'  => redirect('/admin'),
            'vendor' => redirect('/vendor'),
            'runner' => redirect('/runner'),
            default  => redirect('/'),
        };
    }

    /** Ensure a valid active event ID exists */
    private function getOrCreateActiveEventId(?int $eventId = null): int
    {
        if ($eventId && Event::where('id', $eventId)->exists()) {
            return (int) $eventId;
        }

        $activeId = Event::where('status', 'active')->value('id');
        if ($activeId) {
            return (int) $activeId;
        }

        $firstId = Event::first()?->id;
        if ($firstId) {
            return (int) $firstId;
        }

        $venue = Venue::firstOrCreate(
            ['name' => 'Main Venue'],
            [
                'map_data'       => ['coordinates' => '0,0'],
                'seating_layout' => ['sections' => []],
            ]
        );

        $event = Event::create([
            'name'       => 'Main Concert Event',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addDays(30),
            'status'     => 'active',
        ]);

        return (int) $event->id;
    }
}
