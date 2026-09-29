<?php

use App\Models\Contact;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Contact page component.
 * Allows users to send messages, inquiries, and questions to the bookstore administrator.
 */
new #[Layout('layouts.public')] #[Title('Contact Us - BookStore')] class extends Component {
    /** @var string Sender's full name */
    public string $name = '';

    /** @var string Sender's email address */
    public string $email = '';

    /** @var string Subject line of message */
    public string $subject = '';

    /** @var string Content body of the message */
    public string $message = '';

    /** @var bool Success state flag */
    public bool $sent = false;

    /**
     * Pre-populate name and email if the user is authenticated.
     */
    public function mount(): void
    {
        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    /**
     * Submit the contact form and store the message in the database.
     */
    public function submit(): void
    {
        $this->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        Contact::create([
            'name'    => $this->name,
            'email'   => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        $this->reset(['subject', 'message']);
        $this->sent = true;

        Flux::toast(variant: 'success', text: __('Your message has been sent to our administrator!'));
    }
};
?>

<div>
    {{-- Header --}}
    <section class="py-16 text-white text-center">
        <div class="mx-auto max-w-4xl px-4">
            <h1 class="text-4xl font-extrabold sm:text-5xl">Contact Support & Admin</h1>
            <p class="mt-4 text-lg text-blue-100 max-w-2xl mx-auto">
                Have a question regarding book orders, catalog recommendations, or general inquiries? We are here to help.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                {{-- Contact Information --}}
                <div class="lg:col-span-5 space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Get In Touch</h2>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400 text-sm">
                            Fill out the form and our administrator team will review and respond to your inquiry promptly.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div>
                                <h3 class="font-bold text-zinc-900 dark:text-white text-base">Office Address</h3>
                                <p class="mt-1 text-sm text-zinc-500">Jl. Jendral Sudirman Kav. 52, Jakarta Selatan, 12190, Indonesia</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div>
                                <h3 class="font-bold text-zinc-900 dark:text-white text-base">Email Support</h3>
                                <p class="mt-1 text-sm text-zinc-500">support@bookstore.com</p>
                                <p class="text-xs text-zinc-400">Average response time: &lt; 24 hours</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div>
                                <h3 class="font-bold text-zinc-900 dark:text-white text-base">Customer Hotline</h3>
                                <p class="mt-1 text-sm text-zinc-500">+62 21 555 8899 / +62 812 8888 9999</p>
                                <p class="text-xs text-zinc-400">Mon - Fri, 09:00 - 18:00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="lg:col-span-7">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        @if($sent)
                            <div class="py-8 text-center">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                    <flux:icon.check-circle class="size-10" />
                                </div>
                                <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">Message Sent Successfully!</h3>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 max-w-md mx-auto">
                                    Thank you for reaching out to us. Our admin team will get back to your email address shortly.
                                </p>
                                <div class="mt-6">
                                    <flux:button wire:click="$set('sent', false)" variant="primary">
                                        Send Another Message
                                    </flux:button>
                                </div>
                            </div>
                        @else
                            <form wire:submit="submit" class="space-y-6">
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <flux:field>
                                        <flux:label>Your Name</flux:label>
                                        <flux:input wire:model="name" placeholder="John Doe" required />
                                        <flux:error name="name" />
                                    </flux:field>

                                    <flux:field>
                                        <flux:label>Email Address</flux:label>
                                        <flux:input wire:model="email" type="email" placeholder="john@example.com" required />
                                        <flux:error name="email" />
                                    </flux:field>
                                </div>

                                <flux:field>
                                    <flux:label>Subject / Topic</flux:label>
                                    <flux:input wire:model="subject" placeholder="e.g. Inquiring about book availability" required />
                                    <flux:error name="subject" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Message</flux:label>
                                    <flux:textarea wire:model="message" rows="5" placeholder="Write your detailed question or message here..." required />
                                    <flux:error name="message" />
                                </flux:field>

                                <flux:button type="submit" variant="primary" class="w-full">
                                    <span wire:loading.remove wire:target="submit">Send Message</span>
                                    <span wire:loading wire:target="submit">Sending...</span>
                                </flux:button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>