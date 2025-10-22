<?php

namespace src;

use PDO;
use Exception;

/**
 * Classe centralisée pour les statistiques et rapports.
 * Gère toutes les analyses de performance (ventes, produits, assistantes).
 * Basée sur les tables: orders, products, users
 */
class AnalyticsManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // --- Statistiques Globales de Ventes ---

    /**
     * Récupère les statistiques globales de ventes pour une période.
     */
    public function getGlobalSalesStats($dateFrom = null, $dateTo = null)
    {
        try {
            $sql = "
            SELECT 
                COUNT(id) AS total_orders,
                COALESCE(SUM(CASE WHEN newstat = 'deliver' THEN quantity ELSE 0 END), 0) AS total_quantity_sold,
                COALESCE(SUM(CASE WHEN newstat = 'deliver' THEN total_price ELSE 0 END), 0) AS total_revenue,
                COALESCE(AVG(CASE WHEN newstat = 'deliver' THEN total_price END), 0) AS average_order_value,
                (SELECT COALESCE(SUM(cout), 0) FROM depense) AS total_expenses,
                COUNT(CASE WHEN newstat = 'deliver' THEN 1 END) AS delivered_orders,
                COUNT(CASE WHEN newstat = 'canceled' THEN 1 END) AS cancelled_orders,
                COUNT(CASE WHEN newstat = 'processing' THEN 1 END) AS inprogress_orders
            FROM orders
        ";

            $params = [];

            if ($dateFrom && $dateTo) {
                $sql .= " WHERE created_at BETWEEN ? AND ?";
                $params = [$dateFrom, $dateTo];
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            error_log("Erreur Stats: " . $e->getMessage());
        }
    }



    /**
     * Récupère les statistiques par statut de commande pour une période.
     */
    public function getOrderStatusStats($dateFrom = null, $dateTo = null)
    {
        try {
            $sql = "
            SELECT 
                newstat AS status, 
                COUNT(id) AS count,
                COALESCE(SUM(total_price), 0) AS total_amount
            FROM orders
        ";

            $params = [];
            if ($dateFrom && $dateTo) {
                $sql .= " WHERE created_at BETWEEN ? AND ? ";
                $params = [$dateFrom, $dateTo];
            }

            $sql .= " GROUP BY newstat ORDER BY count DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getOrderStatusStats: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Récupère l'évolution des ventes pour les N derniers jours.
     */
    public function getSalesEvolution($days = 30)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE(created_at) as period,
                    COALESCE(SUM(CASE WHEN newstat = 'deliver' THEN total_price ELSE 0 END), 0) as total_revenue,
                    COUNT(CASE WHEN newstat = 'deliver' THEN 1 END) as total_orders
                FROM orders 
                WHERE created_at >= CURDATE() - INTERVAL ? DAY
                GROUP BY period
                ORDER BY period ASC
            ");
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getSalesEvolution: " . $e->getMessage());
            return [];
        }
    }

    // --- Statistiques sur les Produits ---

    /**
     * Récupère les produits les plus vendus sur une période.
     */
    public function getTopSellingProducts($dateFrom = null, $dateTo = null)
    {
        try {
            $sql = "
            SELECT 
                p.id,
                p.name,
                u.name AS assistant_name,
                p.country,
                COALESCE(SUM(CASE WHEN o.newstat = 'deliver' THEN o.quantity ELSE 0 END), 0) AS total_sold, 
                COALESCE(SUM(CASE WHEN o.newstat = 'deliver' THEN o.total_price ELSE 0 END), 0) AS total_revenue
            FROM products p
            LEFT JOIN orders o 
                ON p.id = o.product_id
            LEFT JOIN users u 
                ON o.manager_id = u.id
        ";

            $params = [];

            if ($dateFrom && $dateTo) {
                $sql .= " AND o.created_at BETWEEN ? AND ?";
                $params = [$dateFrom, $dateTo];
            }

            $sql .= "
            GROUP BY p.id, p.name, u.name, p.country
            HAVING total_sold > 0
            ORDER BY total_sold DESC
            LIMIT 5
        ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getTopSellingProducts: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Récupère les statistiques détaillées d'un produit.
     */
    public function getProductStats($productId, $dateFrom, $dateTo)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(id) as total_orders,
                    SUM(quantity) as total_quantity,
                    SUM(CASE WHEN newstat = 'deliver' THEN quantity ELSE 0 END) as delivered_quantity,
                    SUM(CASE WHEN newstat = 'deliver' THEN total_price ELSE 0 END) as total_revenue,
                    AVG(CASE WHEN newstat = 'deliver' THEN total_price END) as average_order_value
                FROM orders
                WHERE product_id = ?
                AND created_at BETWEEN ? AND ?
            ");
            $stmt->execute([$productId, $dateFrom, $dateTo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getProductStats: " . $e->getMessage());
            return null;
        }
    }

    // --- Statistiques sur les Assistantes ---

    /**
     * Récupère le classement des assistantes sur une période.
     */
    public function getAssistantsRanking($dateFrom = null, $dateTo = null)
    {
        try {
            $sql = "
                SELECT 
                    u.id,
                    u.name,
                    u.email,
                    COUNT(o.id) as total_orders,
                    COUNT(CASE WHEN o.newstat = 'deliver' THEN 1 END) as delivered_orders,
                    COUNT(CASE WHEN o.newstat = 'canceled' THEN 1 END) as cancelled_orders,
                    COALESCE(SUM(CASE WHEN o.newstat = 'deliver' THEN o.total_price ELSE 0 END), 0) as total_revenue,
                    ROUND(COUNT(CASE WHEN newstat = 'deliver' THEN 1 END) * 100.0 / NULLIF(COUNT(o.id), 0), 2) as conversion_rate
                FROM users u
                LEFT JOIN orders o ON u.id = o.manager_id";

            $params = [];

            if ($dateFrom && $dateTo) {
                $sql .= " AND o.created_at BETWEEN ? AND ?";
                $params = [$dateFrom, $dateTo];
            }

            $sql .= "
                WHERE u.role = 0 AND u.is_active = 1
                GROUP BY u.id, u.name, u.email
                ORDER BY total_revenue DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAssistantsRanking: " . $e->getMessage());
            return [];
        }
    }

    /**
     * @author tchamie 
     * Je vais plutôt recuperer la liste simplement des assistantes actives
     * et les afficher dans le tableau
     * et pour chaque assistante, je vais recuperer les statistiques (fonction deja ecrite)
     */

    public function getActiveAssistants()
    {
        try {
            $stmt = $this->pdo->query("
                SELECT id, name, email
                FROM users
                WHERE role = 0 AND is_active = 1
                ORDER BY name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getActiveAssistants: " . $e->getMessage());
            return [];
        }
    }

    public function getAssistantById($assistantId)
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email FROM users WHERE id = ? AND role = 0");
        $stmt->execute([$assistantId]);
        $assistant = $stmt->fetch(PDO::FETCH_ASSOC);
        return $assistant;
    }

    /**
     * Récupère les statistiques détaillées d'une assistante.
     */
    public function getAssistantStats($assistantId, $dateFrom = null, $dateTo = null)
    {
        try {
            $sql = "
                SELECT 
                    COUNT(o.id) as total_orders,
                    COUNT(CASE WHEN o.newstat = 'deliver' THEN 1 END) as delivered_orders,
                    COUNT(CASE WHEN o.newstat = 'canceled' THEN 1 END) as cancelled_orders,
                    COUNT(CASE WHEN o.newstat = 'processing' THEN 1 END) as inprogress_orders,
                    SUM(CASE WHEN o.newstat = 'deliver' THEN o.total_price ELSE 0 END) as total_revenue,
                    SUM(CASE WHEN o.newstat = 'deliver' THEN o.quantity ELSE 0 END) as total_quantity_sold,
                    ROUND(COUNT(CASE WHEN newstat = 'deliver' THEN 1 END) * 100.0 / NULLIF(COUNT(o.id), 0), 2) as conversion_rate
                FROM orders o
                WHERE o.manager_id = ?";

            $params = [$assistantId];

            if ($dateFrom && $dateTo) {
                $sql .= " AND o.created_at BETWEEN ? AND ?";
                $params[] = $dateFrom;
                $params[] = $dateTo;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAssistantStats: " . $e->getMessage());
            return null;
        }
    }




    /**
     * Récupère les statistiques des commandes d'une assistante.
     */
    public function countAssistantOrders($assistantId, $dateFrom = null, $dateTo = null)
    {
        try {
            $sql = "SELECT COUNT(o.id) as total FROM orders o WHERE o.manager_id = ?";
            $params = [$assistantId];

            if ($dateFrom && $dateTo) {
                $sql .= " AND o.created_at BETWEEN ? AND ?";
                $params[] = $dateFrom;
                $params[] = $dateTo;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Erreur countAssistantOrders: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupère les commandes d'une assistante avec pagination.
     */
    public function getAssistantOrders($assistantId, $dateFrom = null, $dateTo = null, $limit = 25, $offset = 0)
    {
        try {
            $sql = "
                SELECT o.*, p.name as product_name, p.country
                FROM orders o
                LEFT JOIN products p ON o.product_id = p.id
                WHERE o.manager_id = ?";
            $params = [$assistantId];

            if ($dateFrom && $dateTo) {
                $sql .= " AND o.created_at BETWEEN ? AND ?";
                $params[] = $dateFrom;
                $params[] = $dateTo;
            }

            $sql .= " ORDER BY o.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAssistantOrders: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les produits les plus vendus par une assistante.
     */
    public function getAssistantTopProducts($assistantId, $dateFrom = null, $dateTo = null, $limit = 5)
    {
        try {
            $sql = "
                SELECT 
                    p.id,
                    p.name,
                    p.country,
                    COUNT(o.id) as total_orders,
                    SUM(CASE WHEN o.newstat = 'deliver' THEN o.quantity ELSE 0 END) as total_sold,
                    SUM(CASE WHEN o.newstat = 'deliver' THEN o.total_price ELSE 0 END) as total_revenue
                FROM products p
                INNER JOIN orders o ON p.id = o.product_id
                WHERE o.manager_id = ?";
            $params = [$assistantId];

            if ($dateFrom && $dateTo) {
                $sql .= " AND o.created_at BETWEEN ? AND ?";
                $params[] = $dateFrom;
                $params[] = $dateTo;
            }

            $sql .= "
                GROUP BY p.id, p.name, p.country
                HAVING total_sold > 0
                ORDER BY total_sold DESC
                LIMIT " . (int)$limit;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAssistantTopProducts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère l'évolution des ventes d'une assistante (30 derniers jours).
     */
    public function getAssistantSalesEvolution($assistantId, $days = 30)
    {
        try {
            $sql = "
                SELECT 
                    DATE(created_at) as period,
                    COUNT(CASE WHEN newstat = 'deliver' THEN 1 END) as total_orders,
                    COALESCE(SUM(CASE WHEN newstat = 'deliver' THEN total_price ELSE 0 END), 0) as total_revenue
                FROM orders 
                WHERE manager_id = ? AND created_at >= CURDATE() - INTERVAL ? DAY
                GROUP BY period
                ORDER BY period ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$assistantId, $days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAssistantSalesEvolution: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les statistiques par statut pour une assistante.
     */
    public function getAssistantOrderStatusStats($assistantId, $dateFrom = null, $dateTo = null)
    {
        try {
            $sql = "
                SELECT 
                    newstat as status,
                    COUNT(id) as count
                FROM orders
                WHERE manager_id = ?";
            $params = [$assistantId];

            if ($dateFrom && $dateTo) {
                $sql .= " AND created_at BETWEEN ? AND ?";
                $params[] = $dateFrom;
                $params[] = $dateTo;
            }

            $sql .= " GROUP BY newstat";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAssistantOrderStatusStats: " . $e->getMessage());
            return [];
        }
    }

}
