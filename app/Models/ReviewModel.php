<?php
namespace App\Models;
use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'rating', 'review', 'created_at', 'is_approved', 'cust_Id', 'pr_Id'];
    public $timestamps = false;

    // ✅ Average rating for multiple products
    public function getAverageRatingForProducts(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        return $this->select('pr_Id, ROUND(AVG(rating)) as avg_rating')
                    ->whereIn('pr_Id', $productIds)
                    ->groupBy('pr_Id')
                    ->findAll();
    }

    // ✅ Get reviews for a product with pagination support
    public function getLimitedReviewsByProductId($productId, $limit = 4, $offset = 0)
    {
        return $this->select('reviews.*, customer.*')
                    ->join('customer', 'customer.cust_Id = reviews.cust_Id', 'left')
                    ->where('reviews.pr_Id', $productId)
                    ->orderBy('reviews.created_at', 'DESC')
                    ->findAll($limit, $offset); // ← This enables pagination
    }
    

    // ✅ Get total review count for a product
    public function getReviewCountByProductId($productId)
    {
        return $this->where('pr_Id', $productId)->countAllResults();
    }
}
