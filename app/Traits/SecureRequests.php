<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Trait SecureRequests
 * Fournit des méthodes pour sécuriser les requêtes et valider les données
 * 
 * Ce trait implémente des fonctionnalités de sécurité essentielles :
 * - Validation et nettoyage des données entrantes
 * - Protection contre les injections XSS et SQL
 * - Validation sécurisée des fichiers uploadés
 * - Assainissement des données de sortie
 */
trait SecureRequests
{
    /**
     * Valide et nettoie les données entrantes de manière sécurisée
     *
     * @param array $data Les données à valider
     * @param array $rules Les règles de validation Laravel
     * @param array $messages Messages d'erreur personnalisés
     * @return array Les données nettoyées et validées
     * @throws ValidationException Si la validation échoue
     */
    protected function validateSecurely(array $data, array $rules, array $messages = [])
    {
        // Nettoyage des données
        $cleanData = array_map(function($value) {
            if (is_string($value)) {
                // Suppression des caractères spéciaux et scripts
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                // Protection contre les injections SQL
                $value = str_replace(['\'', '"', ';', '*', '=', 'DROP', 'DELETE', 'UPDATE', 'INSERT'], '', $value);
            }
            return $value;
        }, $data);

        // Validation avec les règles fournies
        $validator = Validator::make($cleanData, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $cleanData;
    }

    /**
     * Assainit les données de sortie pour éviter les failles XSS
     *
     * @param mixed $data Données à assainir (string ou array)
     * @return mixed Données assainies
     */
    protected function sanitizeOutput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeOutput'], $data);
        }
        
        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }

    /**
     * Valide un fichier uploadé de manière sécurisée
     *
     * @param \Illuminate\Http\UploadedFile $file Le fichier à valider
     * @param array $allowedTypes Types MIME autorisés
     * @param int $maxSize Taille maximale en octets (défaut 5MB)
     * @return bool true si le fichier est valide
     * @throws ValidationException Si le fichier est invalide
     */
    protected function validateFileSecurely($file, array $allowedTypes, int $maxSize = 5242880)
    {
        if (!$file->isValid()) {
            throw new ValidationException('Invalid file upload.');
        }

        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new ValidationException('Invalid file type.');
        }

        if ($file->getSize() > $maxSize) {
            throw new ValidationException('File size exceeds limit.');
        }

        // Vérification supplémentaire du type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new ValidationException('File type mismatch.');
        }

        return true;
    }
} 