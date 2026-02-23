# Course Discussions - User Relationship Fix

## Problem
The application was throwing a `RelationNotFoundException` error:
```
Call to undefined relationship [user] on model [App\Models\CourseDiscussion]
```

This occurred when trying to access `$discussion->user` or `$reply->user` in blade templates.

## Root Cause
The `CourseDiscussion` and `DiscussionReply` models had been defined with an `author()` relationship method, but the blade templates were trying to access the relationship as `user` instead of `author`.

The relationship was defined as:
```php
public function author(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_id');
}
```

But views were calling:
```blade
{{ $discussion->user->name }}
```

## Solution
Added a `user()` relationship method as an alias to the `author()` relationship in both models. This maintains backward compatibility with the existing blade templates while keeping the semantic `author()` relationship.

### Files Modified

**1. [app/Models/CourseDiscussion.php](app/Models/CourseDiscussion.php)**
- Added `user()` relationship method that returns `belongsTo(User::class, 'user_id')`
- Kept existing `author()` relationship for semantic clarity

**2. [app/Models/DiscussionReply.php](app/Models/DiscussionReply.php)**
- Added `user()` relationship method that returns `belongsTo(User::class, 'user_id')`
- Kept existing `author()` relationship for semantic clarity

### Code Changes

**Before (CourseDiscussion.php):**
```php
public function author(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_id');
}

public function replies(): HasMany
{
    return $this->hasMany(DiscussionReply::class, 'discussion_id');
}
```

**After (CourseDiscussion.php):**
```php
public function author(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_id');
}

public function user(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_id');
}

public function replies(): HasMany
{
    return $this->hasMany(DiscussionReply::class, 'discussion_id');
}
```

Same pattern applied to DiscussionReply.php.

## Views Using This Relationship
- [resources/views/admin/discussions/index.blade.php](resources/views/admin/discussions/index.blade.php) - Lines 70-71
- [resources/views/admin/discussions/show.blade.php](resources/views/admin/discussions/show.blade.php) - Lines 41, 79, 118
- [resources/views/courses/discussions/index.blade.php](resources/views/courses/discussions/index.blade.php) - Line 49

## Result
✅ No more "Call to undefined relationship [user]" errors  
✅ Discussion author information displays correctly in all views  
✅ Both `author()` and `user()` relationships are available for use  
✅ Backward compatibility maintained with existing blade templates

## Notes
- The dual relationship approach allows for semantic clarity (`author()`) while supporting existing template code (`user()`)
- Both methods access the same `user_id` foreign key
- No database changes required
