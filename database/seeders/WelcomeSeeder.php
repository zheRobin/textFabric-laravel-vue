<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WelcomeData;

class WelcomeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'firstBlock' => [
                'title' => [
                    'en' => "Welcome to the world of efficient text creation - Welcome to TEXTHUB!",
                    'de' => "Willkommen in der Welt der effizienten Texterstellung - Willkommen bei TEXTHUB!"
                ],
                'value' => [
                    'en' => "Our application is the ultimate solution for businesses that need thousands of professional product texts, marketing texts, descriptions, bullet points, keywords, synonyms, features and benefits without compromising on quality. Witness how we revolutionize the way you generate content and help increase your productivity and drive business success.",
                    'de' => "Unsere Anwendung stellt die ultimative Lösung für Unternehmen dar, die tausende von professionellen Produkttexten, Marketingtexten, Beschreibungen, Bullet Points, Keywords, Synonyme, Features und Benefits benötigen, ohne dabei Kompromisse bei der Qualität eingehen zu wollen. Werden Sie Zeuge, wie wir die Art und Weise, wie Sie Inhalte generieren, revolutionieren und dabei helfen, Ihre Produktivität zu steigern und Ihren Geschäftserfolg voranzutreiben."
                ],
                'link' => [
                    'name' => [
                        'en' => null,
                        'de' => null
                    ],
                    'value' => null
                ],
                'icon' => "fa-solid fa-video"
            ],
            'secondBlock' => [
                'title' => [
                    'en' => "Overview video",
                    'de' => "Übersichtsvideo"
                ],
                'value' => [
                    'en' => "In this practical video you will learn step by step how to use the TEXTHUB application for automatic text generation. From the initial installation, to understanding the various functions, to generating high-quality texts, everything will be explained to you in detail.",
                    'de' => "In diesem praktischen Video lernen Sie Schritt für Schritt, wie Sie die TEXTHUB-Anwendung zur automatischen Texterzeugung nutzen. Von der Erstinstallation, über das Verstehen der verschiedenen Funktionen, bis hin zur Erzeugung von qualitativ hochwertigen Texten wird Ihnen alles detailliert erklärt."
                ],
                'link' => [
                    'name' => [
                        'en' => "Watch video",
                        'de' => "Video ansehen"
                    ],
                    'value' => "https://www.texthub.io"
                ],
                'icon' => "fa-solid fa-video"
            ],
            'thirdBlock' => [
                'title' => [
                    'en' => "Customize language models",
                    'de' => "Sprachmodelle individualisieren"
                ],
                'value' => [
                    'en' => "In this video, we show you how to configure language models in the editor and adapt them to your needs. Whether product texts, synonyms, bullet points or job references - we create all kinds of texts.",
                    'de' => "In diesem Video zeigen wir Ihnen, wie Sie Sprachmodelle im Editor konfigurieren und auf Ihre Bedürfnisse anpassen. Egal ob Produkttexte, Synonyme, Bulletpoints oder Arbeitszeugnisse - wir erstellen alle Arten von Texten."
                ],
                'link' => [
                    'name' => [
                        'en' => "Watch video",
                        'de' => "Video ansehen"
                    ],
                    'value' => "https://www.texthub.io"
                ],
                'icon' => "fa-solid fa-pen-to-square"
            ],
            'fourthBlock' => [
                'title' => [
                    'en' => "Export and translations",
                    'de' => "Export und Übersetzungen"
                ],
                'value' => [
                    'en' => "In this helpful how-to video, you will learn how to use TEXTHUB to seamlessly export your created content, have it translated into different languages, and efficiently increase your content creation productivity.",
                    'de' => "In diesem hilfreichen How-to-Video lernen Sie, wie Sie mit TEXTHUB Ihre erstellten Inhalte nahtlos exportieren, diese in verschiedene Sprachen übersetzen lassen und auf effiziente Weise die Produktivität Ihrer Inhaltserstellung steigern."
                ],
                'link' => [
                    'name' => [
                        'en' => "Watch video",
                        'de' => "Video ansehen"
                    ],
                    'value' => "https://www.texthub.io"
                ],
                'icon' => "fa-solid fa-file-export"
            ],
            'fifthBlock' => [
                'title' => [
                    'en' => "Help and support",
                    'de' => "Hilfe und Support"
                ],
                'value' => [
                    'en' => "Do you have questions about a function or need advice on how to achieve your goal most quickly? Our team will be happy to answer your questions and provide valuable tips that will save you time and money.",
                    'de' => "Sie haben Fragen zu einer Funktion oder brauchen Rat, wie Sie Ihr Ziel am schnellsten erreichen? Unser Team steht Ihnen sehr gerne bei Fragen zur Seite und gibt wertvolle Tipps, welche Zeit und Kosten sparen."
                ],
                'link' => [
                    'name' => [
                        'en' => "Watch video",
                        'de' => "Video ansehen"
                    ],
                    'value' => "https://www.texthub.io/kontakt"
                ],
                'icon' => "fa-solid fa-circle-question"
            ],
        ];

        foreach ($data as $block => $content) {
            WelcomeData::create([
                'block_name' => $block,
                'title' => $content['title'],
                'value' => $content['value'],
                'link_name' => $content['link']['name'] ?? null,
                'link' => $content['link']['value'] ?? null,
                'icon' => $content['icon'],
            ]);
        }
    }
}

