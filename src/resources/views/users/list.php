<?php \core\Views::extends('main'); ?>

<?php \core\Views::section('styles') ?>
<link href="../../../resources/css/list.css" rel="stylesheet" />
<?php \core\Views::endsection() ?>

<?php \core\Views::section('title') ?>
UserHub | Users
<?php \core\Views::endsection() ?>

<?php \core\Views::section('content') ?>
<div class="layout-container">
    <div class="content-card">
        <div class="page-header">
            <h1 class="page-title">Users List</h1>
            <a href="/users/create" class="btn btn-success"><b>Add New User </b> <i class="fa-solid fa-user-plus"></i></a>
        </div>

        <?php if ($users->rowCount() > 0): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                    <tr>
                        <th>
                            <a href="#" data-sort-by="id">
                                ID
                                <span class="sort-indicator"></span>
                            </a>
                        </th>
                        <th>
                            <a href="#" data-sort-by="username">
                                Username
                                <span class="sort-indicator"></span>
                            </a>
                        </th>
                        <th>
                            <a href="#" data-sort-by="first_name">
                                First Name
                                <span class="sort-indicator"></span>
                            </a>
                        </th>
                        <th>
                            <a href="#" data-sort-by="last_name">
                                Last Name
                                <span class="sort-indicator"></span>
                            </a>
                        </th>
                        <th>Gender</th>
                        <th>Birthdate</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($row['gender'])); ?></td>
                            <td><?php echo htmlspecialchars($row['birthdate']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="/users/<?php echo $row['id']; ?>" class="btn"><i class="fa-solid fa-eye"></i></a>
                                    <a href="/users/<?php echo $row['id']; ?>/edit" class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="/users/<?php echo $row['id']; ?>/delete"
                                       class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this user?')"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pagination->total_pages() > 1): ?>
                <div class="pagination">
                    <?php if ($pagination->has_previous_page()): ?>
                        <a href="/users?page=<?php echo $pagination->previous_page(); ?>">&laquo; Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination->total_pages(); $i++): ?>
                        <?php if ($i == $pagination->current_page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="/users?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($pagination->has_next_page()): ?>
                        <a href="/users?page=<?php echo $pagination->next_page(); ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-state">
                <p>No users found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php \core\Views::endsection() ?>

<?php \core\Views::section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const sortableHeaders = document.querySelectorAll('th a');
        
        const urlParams = new URLSearchParams(window.location.search);
        const currentSortBy = urlParams.get('sort_by');
        const currentSortOrder = urlParams.get('sort_order');
        const currentPage = urlParams.get('page') || '1'; 
        
        updateSortIndicators(currentSortBy, currentSortOrder);
        
        sortableHeaders.forEach(header => {
            header.addEventListener('click', function(e) {
                e.preventDefault();
                
                const columnText = this.textContent.trim();
                let sortBy;
                
                switch(columnText) {
                    case 'ID':
                        sortBy = 'id';
                        break;
                    case 'Username':
                        sortBy = 'username';
                        break;
                    case 'First Name':
                        sortBy = 'first_name';
                        break;
                    case 'Last Name':
                        sortBy = 'last_name';
                        break;
                    default:
                        return; 
                }
                
                let nextSortOrder;
                if (currentSortBy === sortBy) {
                    
                    if (currentSortOrder === 'ASC') {
                        nextSortOrder = 'DESC';
                    } else if (currentSortOrder === 'DESC') {
                        
                        nextSortOrder = null;
                    } else {
                        nextSortOrder = 'ASC';
                    }
                } else {
                    
                    nextSortOrder = 'ASC';
                }

                window.location.href = buildSortUrl(sortBy, nextSortOrder, currentPage);
            });
        });

        
        function buildSortUrl(sortBy, sortOrder, page) {
            const url = new URL(window.location.href);
            
            if (sortOrder === null) {
                url.searchParams.delete('sort_by');
                url.searchParams.delete('sort_order');
            } else {
                
                url.searchParams.set('sort_by', sortBy);
                url.searchParams.set('sort_order', sortOrder);
            }
            
            url.searchParams.set('page', page);

            return url.toString();
        }
        
        function updateSortIndicators(sortBy, sortOrder) {
            
            const allIndicators = document.querySelectorAll('.sort-indicator');
            allIndicators.forEach(indicator => {
                indicator.innerHTML = '';
            });
            
            if (sortBy && sortOrder) {
                let headerText;
                
                switch(sortBy) {
                    case 'id':
                        headerText = 'ID';
                        break;
                    case 'username':
                        headerText = 'Username';
                        break;
                    case 'first_name':
                        headerText = 'First Name';
                        break;
                    case 'last_name':
                        headerText = 'Last Name';
                        break;
                    default:
                        return;
                }
                
                const headers = document.querySelectorAll('th a');
                let targetHeader;

                headers.forEach(header => {
                    if (header.textContent.trim() === headerText) {
                        targetHeader = header;
                    }
                });

                if (targetHeader) {
                    const indicator = targetHeader.querySelector('.sort-indicator');
                    if (indicator) {
                        
                        if (sortOrder === 'ASC') {
                            indicator.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
                        } else if (sortOrder === 'DESC') {
                            indicator.innerHTML = '<i class="fa-solid fa-arrow-down"></i>';
                        }
                    }
                }
            }
        }
    });
</script>
<?php \core\Views::endsection() ?>

<?php \core\Views::endpush() ?>
