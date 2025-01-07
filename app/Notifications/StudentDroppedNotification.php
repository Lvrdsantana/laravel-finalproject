<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Notification envoyée lorsqu'un étudiant est "droppé" d'un cours
 * 
 * Cette notification est déclenchée lorsque le taux de présence d'un étudiant
 * tombe en dessous du seuil minimal requis (généralement 30%) pour un cours donné.
 * Elle est envoyée aux coordinateurs et à l'enseignant responsable du cours.
 * 
 * La notification contient les informations suivantes :
 * - Nom de l'étudiant
 * - Nom du cours
 * - Taux de présence
 * - Nombre total de sessions
 * - Nombre de sessions où l'étudiant était présent
 * - IDs de l'étudiant et du cours
 */
class StudentDroppedNotification extends Notification
{
    use Queueable; // Permet la mise en file d'attente de la notification

    /** @var string Nom de l'étudiant concerné */
    protected $studentName;
    
    /** @var string Nom du cours duquel l'étudiant est droppé */
    protected $courseName;
    
    /** @var float Taux de présence en pourcentage (ex: 25.5) */
    protected $attendanceRate;
    
    /** @var int|null Nombre total de sessions du cours jusqu'à présent */
    protected $totalSessions;
    
    /** @var int|null Nombre de sessions auxquelles l'étudiant a assisté */
    protected $presentSessions;
    
    /** @var int|null ID unique de l'étudiant dans la base de données */
    protected $studentId;
    
    /** @var int|null ID unique du cours dans la base de données */
    protected $courseId;

    /**
     * Crée une nouvelle instance de notification de drop
     * 
     * Initialise tous les attributs nécessaires et crée un ID unique pour la notification.
     * Enregistre également un log de création pour le suivi et le debugging.
     *
     * @param string $studentName Nom complet de l'étudiant
     * @param string $courseName Intitulé du cours
     * @param float $attendanceRate Taux de présence calculé
     * @param int|null $totalSessions Nombre total de sessions (optionnel)
     * @param int|null $presentSessions Nombre de présences (optionnel)
     * @param int|null $studentId ID de l'étudiant (optionnel)
     * @param int|null $courseId ID du cours (optionnel)
     */
    public function __construct($studentName, $courseName, $attendanceRate, $totalSessions = null, $presentSessions = null, $studentId = null, $courseId = null)
    {
        // Génère un UUID unique pour cette notification
        $this->id = Str::uuid()->toString();
        
        // Stocke les informations de base
        $this->studentName = $studentName;
        $this->courseName = $courseName;
        $this->attendanceRate = $attendanceRate;
        $this->totalSessions = $totalSessions;
        $this->presentSessions = $presentSessions;
        $this->studentId = $studentId;
        $this->courseId = $courseId;

        // Enregistre un log pour le suivi et le debugging
        Log::info('Drop notification created', [
            'student_id' => $this->studentId,
            'course_id' => $this->courseId,
            'total_sessions' => $this->totalSessions,
            'present_sessions' => $this->presentSessions,
            'rate' => $this->attendanceRate
        ]);
    }

    /**
     * Détermine les canaux de diffusion de la notification
     * 
     * Actuellement, seul le canal 'database' est utilisé pour stocker
     * la notification dans la base de données.
     * 
     * @param mixed $notifiable Entité recevant la notification
     * @return array Canaux de diffusion (uniquement 'database' pour le moment)
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Prépare la notification pour stockage en base de données
     * 
     * Formate le message de notification et structure toutes les données
     * nécessaires pour l'enregistrement dans la table des notifications.
     * Inclut également un log pour le suivi.
     *
     * @param mixed $notifiable Entité recevant la notification
     * @return array Données formatées pour la base de données
     */
    public function toDatabase($notifiable)
    {
        // Construction du message principal
        $message = "Student {$this->studentName} has been dropped from course {$this->courseName}";
        $message .= " (Attendance rate: {$this->attendanceRate}%)";
        
        // Ajout des détails de présence si disponibles
        if ($this->totalSessions && $this->presentSessions) {
            $message .= " - Present for {$this->presentSessions}/{$this->totalSessions} sessions";
        }

        // Préparation des données complètes
        $data = [
            'id' => $this->id,
            'type' => 'student_dropped',
            'student_name' => $this->studentName,
            'course_name' => $this->courseName,
            'attendance_rate' => $this->attendanceRate,
            'total_sessions' => $this->totalSessions,
            'present_sessions' => $this->presentSessions,
            'student_id' => $this->studentId,
            'course_id' => $this->courseId,
            'message' => $message
        ];

        // Log de l'envoi pour traçabilité
        Log::info('Drop notification sent to database', $data);

        return $data;
    }

    /**
     * Convertit la notification en tableau pour sérialisation
     * 
     * Cette méthode est utilisée quand la notification doit être
     * convertie en JSON, par exemple pour une réponse API ou
     * pour le stockage. Elle retourne toutes les données pertinentes
     * sans le message formaté.
     *
     * @param mixed $notifiable Entité recevant la notification
     * @return array Données  de la notification
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->id,
            'type' => 'student_dropped',
            'student_name' => $this->studentName,
            'course_name' => $this->courseName,
            'attendance_rate' => $this->attendanceRate,
            'total_sessions' => $this->totalSessions,
            'present_sessions' => $this->presentSessions,
            'student_id' => $this->studentId,
            'course_id' => $this->courseId
        ];
    }
}